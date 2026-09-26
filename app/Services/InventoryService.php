<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Increase stock on purchase or sales return.
     */
    public function addStock(int $productId, string $batchNumber, string $expiryDate, int $quantity, float $purchasePrice, float $sellingPrice): void
    {
        DB::transaction(function () use ($productId, $batchNumber, $expiryDate, $quantity, $purchasePrice, $sellingPrice) {
            $product = Product::findOrFail($productId);
            $product->increment('current_stock', $quantity);

            $batch = ProductBatch::firstOrNew([
                'product_id' => $productId,
                'batch_number' => $batchNumber,
            ]);

            $batch->expiry_date = $expiryDate;
            $batch->quantity = ($batch->quantity ?? 0) + $quantity;
            $batch->purchase_price = $purchasePrice;
            $batch->selling_price = $sellingPrice;
            $batch->save();
        });
    }

    /**
     * Decrease stock on sale or purchase return.
     */
    public function reduceStock(int $productId, ?string $batchNumber, int $quantity): void
    {
        DB::transaction(function () use ($productId, $batchNumber, $quantity) {
            $product = Product::findOrFail($productId);
            $product->decrement('current_stock', $quantity);

            if ($batchNumber) {
                $batch = ProductBatch::where('product_id', $productId)
                    ->where('batch_number', $batchNumber)
                    ->first();

                if ($batch) {
                    $batch->decrement('quantity', min($batch->quantity, $quantity));
                }
            } else {
                // FIFO batch reduction if no batch specified
                $remaining = $quantity;
                $batches = ProductBatch::where('product_id', $productId)
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc')
                    ->get();

                foreach ($batches as $b) {
                    if ($remaining <= 0) break;
                    $deduct = min($b->quantity, $remaining);
                    $b->decrement('quantity', $deduct);
                    $remaining -= $deduct;
                }
            }
        });
    }
}
