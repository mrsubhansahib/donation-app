<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_payment_id',
        'amount',
        'currency',
        'status',
        'paid_at',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
