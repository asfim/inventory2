<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_code',
        'sku',
        'barcode',
        'category_id',
        'brand_id',
        'active_ingredient',
        'unit_id',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'min_stock',
        'max_stock',
        'current_stock',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'min_stock' => 'integer',
            'max_stock' => 'integer',
            'current_stock' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function activeBatches()
    {
        return $this->hasMany(ProductBatch::class)->where('quantity', '>', 0);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function getStockValueAttribute(): float
    {
        return $this->current_stock * $this->purchase_price;
    }
}
