<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


#[Fillable([
    'user_id',
    'date',
    'check_in',
    'check_out',
    'late_minutes',
    'lat',
    'lng',
    'status'
])]
class Attendance extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
