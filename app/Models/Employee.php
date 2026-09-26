<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'photo',
        'phone',
        'email',
        'address',
        'designation',
        'joining_date',
        'salary',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'salary' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }
}
