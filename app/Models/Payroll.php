<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id', 'month', 'year', 'basic_salary',
    'deductions', 'net_salary', 'details', 'status'
])]
class Payroll extends Model
{
    protected $casts = [
    'details' => 'array',
];

public function user() {
    return $this->belongsTo(User::class);
}
}