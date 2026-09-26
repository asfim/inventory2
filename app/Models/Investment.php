<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = [
        'investor_name',
        'investment_date',
        'amount',
        'payment_method_id',
        'reference',
        'note',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'investment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
