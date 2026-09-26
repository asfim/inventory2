<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'account_number',
        'account_holder',
        'opening_balance',
        'current_balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }
}
