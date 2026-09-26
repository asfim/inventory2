<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Services\AuditLogService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function overview(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Product::with(['category', 'unit', 'batches']);

        if ($status === 'low') {
            $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
        } elseif ($status === 'out') {
            $query->where('current_stock', '<=', 0);
        } elseif ($status === 'in') {
            $query->whereColumn('current_stock', '>', 'min_stock');
        }

        $products = $query->orderBy('id', 'desc')->paginate(20);
        $totalStockValue = Product::all()->sum(fn($p) => $p->current_stock * $p->purchase_price);

        return view('stock.overview', compact('products', 'status', 'totalStockValue'));
    }

    public function adjustments()
    {
        $adjustments = StockAdjustment::with(['items.product', 'creator'])->orderBy('id', 'desc')->paginate(15);
        $products = Product::where('status', true)->get();
        return view('stock.adjustments', compact('adjustments', 'products'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'type' => 'required|in:increase,decrease',
            'reason' => 'required|in:Damage,Expired,Lost,Correction,Opening Stock,Other',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, &$adjustment) {
            $adjNo = 'ADJ-' . date('Ymd') . '-' . str_pad(StockAdjustment::count() + 1, 4, '0', STR_PAD_LEFT);

            $adjustment = StockAdjustment::create([
                'adjustment_no' => $adjNo,
                'adjustment_date' => $validated['adjustment_date'],
                'type' => $validated['type'],
                'reason' => $validated['reason'],
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'quantity' => $item['quantity'],
                ]);

                if ($validated['type'] === 'increase') {
                    $batchNo = $item['batch_number'] ?? 'ADJ-BATCH';
                    $expiry = now()->addYears(2)->toDateString();
                    $product = Product::find($item['product_id']);
                    $this->inventoryService->addStock(
                        $item['product_id'],
                        $batchNo,
                        $expiry,
                        $item['quantity'],
                        $product->purchase_price,
                        $product->selling_price
                    );
                } else {
                    $this->inventoryService->reduceStock(
                        $item['product_id'],
                        $item['batch_number'] ?? null,
                        $item['quantity']
                    );
                }
            }
        });

        AuditLogService::log('stock_adjustment', StockAdjustment::class, $adjustment->id, "স্টক এডজাস্টমেন্ট সম্পন্ন হয়েছে: {$adjustment->adjustment_no}");

        return back()->with('success', 'স্টক এডজাস্টমেন্ট সফলভাবে সংরক্ষণ করা হয়েছে!');
    }

    public function transfers()
    {
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'items.product', 'creator'])->orderBy('id', 'desc')->paginate(15);
        $branches = Branch::where('status', true)->get();
        $products = Product::where('status', true)->where('current_stock', '>', 0)->get();
        return view('stock.transfers', compact('transfers', 'branches', 'products'));
    }

    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'transfer_date' => 'required|date',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, &$transfer) {
            $transNo = 'TRF-' . date('Ymd') . '-' . str_pad(StockTransfer::count() + 1, 4, '0', STR_PAD_LEFT);

            $transfer = StockTransfer::create([
                'transfer_no' => $transNo,
                'transfer_date' => $validated['transfer_date'],
                'from_branch_id' => $validated['from_branch_id'],
                'to_branch_id' => $validated['to_branch_id'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        AuditLogService::log('stock_transfer', StockTransfer::class, $transfer->id, "ব্রাঞ্চ স্টক ট্রান্সফার সম্পন্ন: {$transfer->transfer_no}");

        return back()->with('success', 'ব্রাঞ্চ স্টক ট্রান্সফার সম্পন্ন হয়েছে!');
    }
}
