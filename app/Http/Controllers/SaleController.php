<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Services\AuditLogService;
use App\Services\InventoryService;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService,
        protected LedgerService $ledgerService
    ) {}

    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'paymentMethod', 'seller']);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        }

        $sales = $query->orderBy('id', 'desc')->paginate(15);
        $customers = Customer::where('status', true)->get();

        return view('sales.index', compact('sales', 'customers'));
    }

    public function pos()
    {
        $categories = Category::where('status', true)->get();
        $customers = Customer::where('status', true)->get();
        $paymentMethods = PaymentMethod::where('status', true)->get();
        $products = Product::with(['unit', 'category', 'activeBatches'])->where('status', true)->where('current_stock', '>', 0)->get();
        $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('sales.pos', compact('categories', 'customers', 'paymentMethods', 'products', 'invoiceNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|string|unique:sales,invoice_no',
            'sale_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, &$sale) {
            $totalAmount = $validated['total_amount'];
            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxAmount = $validated['tax_amount'] ?? 0;
            $netAmount = max(0, $totalAmount - $discountAmount + $taxAmount);
            $paidAmount = min($netAmount, $validated['paid_amount']);
            $dueAmount = max(0, $netAmount - $paidAmount);
            $changeAmount = max(0, $validated['paid_amount'] - $netAmount);

            $paymentStatus = 'paid';
            if ($dueAmount > 0) {
                $paymentStatus = ($paidAmount > 0) ? 'partial' : 'due';
            }

            $sale = Sale::create([
                'invoice_no' => $validated['invoice_no'],
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'change_amount' => $changeAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'payment_status' => $paymentStatus,
                'seller_id' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $subtotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $subtotal,
                ]);

                // Auto reduce stock
                $this->inventoryService->reduceStock(
                    $item['product_id'],
                    $item['batch_number'] ?? null,
                    $item['quantity']
                );
            }

            // Customer ledger record (Net Amount debited as sale, Paid Amount credited)
            $this->ledgerService->recordCustomerTransaction(
                $validated['customer_id'],
                'sale',
                $sale->invoice_no,
                $netAmount,
                $paidAmount,
                "বিক্রয় মেমো #{$sale->invoice_no}"
            );

            // Payment method cash in if paid > 0
            if ($paidAmount > 0) {
                $this->ledgerService->recordPaymentTransaction(
                    $validated['payment_method_id'],
                    'cash_in',
                    'Sales',
                    $paidAmount,
                    $sale->invoice_no,
                    "বিক্রয় মেমো #{$sale->invoice_no} মূল্য প্রাপ্তি"
                );
            }
        });

        AuditLogService::log('create_sale', Sale::class, $sale->id, "নতুন বিক্রয় ইনভয়েস তৈরি করা হয়েছে: {$sale->invoice_no}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'বিক্রয় সফলভাবে সম্পন্ন হয়েছে!',
                'sale_id' => $sale->id,
                'invoice_url' => route('sales.show', $sale->id),
                'print_url' => route('sales.print', $sale->id),
            ]);
        }

        return redirect()->route('sales.show', $sale->id)->with('success', 'বিক্রয় সফলভাবে সম্পন্ন হয়েছে!');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.product.unit', 'paymentMethod', 'seller']);
        return view('sales.show', compact('sale'));
    }

    public function printInvoice(Sale $sale)
    {
        $sale->load(['customer', 'items.product.unit', 'paymentMethod', 'seller']);
        return view('sales.print', compact('sale'));
    }

    public function returnView(Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'paymentMethod']);
        $paymentMethods = PaymentMethod::where('status', true)->get();
        return view('sales.return', compact('sale', 'paymentMethods'));
    }

    public function storeReturn(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'return_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($sale, $validated, &$sReturn) {
            $totalAmount = 0;
            $returnNo = 'SRET-' . date('Ymd') . '-' . str_pad(SalesReturn::count() + 1, 4, '0', STR_PAD_LEFT);

            foreach ($validated['items'] as $item) {
                if (($item['quantity'] ?? 0) > 0) {
                    $totalAmount += $item['quantity'] * $item['unit_price'];
                }
            }

            $sReturn = SalesReturn::create([
                'return_no' => $returnNo,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_date' => $validated['return_date'],
                'total_amount' => $totalAmount,
                'payment_method_id' => $validated['payment_method_id'],
                'reason' => $validated['reason'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                if (($item['quantity'] ?? 0) > 0) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    SalesReturnItem::create([
                        'sales_return_id' => $sReturn->id,
                        'product_id' => $item['product_id'],
                        'batch_number' => $item['batch_number'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                    ]);

                    // Increase stock back
                    $batchNumber = $item['batch_number'] ?? 'DEFAULT';
                    $expiryDate = now()->addYear()->toDateString();
                    $this->inventoryService->addStock(
                        $item['product_id'],
                        $batchNumber,
                        $expiryDate,
                        $item['quantity'],
                        0,
                        $item['unit_price']
                    );
                }
            }

            // Customer ledger update (Return credited to customer balance)
            $this->ledgerService->recordCustomerTransaction(
                $sale->customer_id,
                'return',
                $returnNo,
                0,
                $totalAmount,
                "বিক্রয় ফেরত #{$returnNo}"
            );

            // Cash Out if refund paid to customer
            $this->ledgerService->recordPaymentTransaction(
                $validated['payment_method_id'],
                'cash_out',
                'Sales Return',
                $totalAmount,
                $returnNo,
                "বিক্রয় ফেরত টাকা প্রদান #{$returnNo}"
            );
        });

        AuditLogService::log('sales_return', SalesReturn::class, $sReturn->id, "সেলস রিটার্ন সম্পন্ন হয়েছে: {$sReturn->return_no}");

        return redirect()->route('sales.show', $sale->id)->with('success', 'সেলস রিটার্ন সফলভাবে সম্পাদন করা হয়েছে!');
    }
}
