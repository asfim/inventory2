<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_number',
        'expiry_date',
        'quantity',
        'purchase_price',
        'selling_price',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'quantity' => 'integer',
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return (int) Carbon::now()->startOfDay()->diffInDays($this->expiry_date, false);
    }

    public function getExpiryStatusAttribute(): string
    {
        $days = $this->days_until_expiry;
        if ($days < 0) {
            return 'expired';
        } elseif ($days <= 30) {
            return 'expiring_30';
        } elseif ($days <= 60) {
            return 'expiring_60';
        } elseif ($days <= 90) {
            return 'expiring_90';
        }
        return 'valid';
    }
}
