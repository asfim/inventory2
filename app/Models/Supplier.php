<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'company_name',
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

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function ledgers()
    {
        return $this->hasMany(SupplierLedger::class)->orderBy('date', 'desc')->orderBy('id', 'desc');
    }
}
