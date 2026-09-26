<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'opening_due',
        'current_due',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_due' => 'decimal:2',
            'current_due' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class)->orderBy('date', 'desc')->orderBy('id', 'desc');
    }
}
