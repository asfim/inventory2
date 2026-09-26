<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Services\AuditLogService;
use App\Services\InventoryService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected LedgerService $ledgerService
    ) {}

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'paymentMethod', 'creator']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        $purchases = $query->orderBy('id', 'desc')->paginate(15);
        $suppliers = Supplier::where('status', true)->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', true)->get();
        $products = Product::with(['unit', 'category'])->where('status', true)->get();
        $paymentMethods = PaymentMethod::where('status', true)->get();
        $invoiceNo = 'PUR-' . date('Ymd') . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('purchases.create', compact('suppliers', 'products', 'paymentMethods', 'invoiceNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:purchases,invoice_no',
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'required|string',
            'items.*.expiry_date' => 'required|date',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, &$purchase) {
            $totalAmount = $validated['total_amount'];
            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxAmount = $validated['tax_amount'] ?? 0;
            $netAmount = max(0, $totalAmount - $discountAmount + $taxAmount);
            $paidAmount = min($netAmount, $validated['paid_amount']);
            $dueAmount = max(0, $netAmount - $paidAmount);

            $paymentStatus = 'due';
            if ($paidAmount >= $netAmount) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0) {
                $paymentStatus = 'partial';
            }

            $purchase = Purchase::create([
                'invoice_no' => $validated['invoice_no'],
                'purchase_date' => $validated['purchase_date'],
                'supplier_id' => $validated['supplier_id'],
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'payment_status' => $paymentStatus,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0) + ($item['tax'] ?? 0);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'tax' => $item['tax'] ?? 0,
                    'subtotal' => $subtotal,
                ]);

                // Auto increase stock & update batch
                $this->inventoryService->addStock(
                    $item['product_id'],
                    $item['batch_number'],
                    $item['expiry_date'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['selling_price']
                );
            }

            // Supplier ledger update (Net Amount credited as purchase, Paid Amount debited)
            $this->ledgerService->recordSupplierTransaction(
                $validated['supplier_id'],
                'purchase',
                $purchase->invoice_no,
                $paidAmount,
                $netAmount,
                "পারচেজ চালান নং {$purchase->invoice_no}"
            );

            // Payment method cash out if paid > 0
            if ($paidAmount > 0) {
                $this->ledgerService->recordPaymentTransaction(
                    $validated['payment_method_id'],
                    'cash_out',
                    'Purchase Payment',
                    $paidAmount,
                    $purchase->invoice_no,
                    "পারচেজ চালান নং {$purchase->invoice_no} পরিশোধ"
                );
            }
        });

        AuditLogService::log('create_purchase', Purchase::class, $purchase->id, "নতুন পারচেজ ইনভয়েস তৈরি করা হয়েছে: {$purchase->invoice_no}");

        return redirect()->route('purchases.show', $purchase->id)->with('success', 'পারচেজ সফলভাবে সম্পন্ন হয়েছে এবং স্টক বৃদ্ধি পেয়েছে!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product.unit', 'paymentMethod', 'creator']);
        return view('purchases.show', compact('purchase'));
    }

    public function printInvoice(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product.unit', 'paymentMethod', 'creator']);
        return view('purchases.print', compact('purchase'));
    }

    public function returnView(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'paymentMethod']);
        $paymentMethods = PaymentMethod::where('status', true)->get();
        return view('purchases.return', compact('purchase', 'paymentMethods'));
    }

    public function storeReturn(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'return_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($purchase, $validated, &$pReturn) {
            $totalAmount = 0;
            $returnNo = 'PRET-' . date('Ymd') . '-' . str_pad(PurchaseReturn::count() + 1, 4, '0', STR_PAD_LEFT);

            foreach ($validated['items'] as $item) {
                if (($item['quantity'] ?? 0) > 0) {
                    $totalAmount += $item['quantity'] * $item['unit_price'];
                }
            }

            $pReturn = PurchaseReturn::create([
                'return_no' => $returnNo,
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'return_date' => $validated['return_date'],
                'total_amount' => $totalAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'reason' => $validated['reason'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                if (($item['quantity'] ?? 0) > 0) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    PurchaseReturnItem::create([
                        'purchase_return_id' => $pReturn->id,
                        'product_id' => $item['product_id'],
                        'batch_number' => $item['batch_number'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                    ]);

                    // Decrease stock
                    $this->inventoryService->reduceStock($item['product_id'], $item['batch_number'], $item['quantity']);
                }
            }

            // Supplier ledger update (Return decreases supplier balance)
            $this->ledgerService->recordSupplierTransaction(
                $purchase->supplier_id,
                'return',
                $returnNo,
                $totalAmount,
                0,
                "পারচেজ রিটার্ন #{$returnNo}"
            );

            // Cash In if cash received back
            $this->ledgerService->recordPaymentTransaction(
                $validated['payment_method_id'],
                'cash_in',
                'Purchase Return',
                $totalAmount,
                $returnNo,
                "পারচেজ রিটার্ন ফেরত টাকা গ্রহণ #{$returnNo}"
            );
        });

        AuditLogService::log('purchase_return', PurchaseReturn::class, $pReturn->id, "পারচেজ রিটার্ন সম্পন্ন হয়েছে: {$pReturn->return_no}");

        return redirect()->route('purchases.show', $purchase->id)->with('success', 'পারচেজ রিটার্ন সফলভাবে সম্পাদন করা হয়েছে!');
    }
}
