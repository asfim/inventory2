<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'phone', 'address', 'is_main', 'status'];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'status' => 'boolean',
        ];
    }
}
