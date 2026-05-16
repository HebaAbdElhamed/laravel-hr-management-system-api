<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'type',
    'start_date',
    'end_date',
    'days_requested',
    'status',
    'reason'
])]
class Leave extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
