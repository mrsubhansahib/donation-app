<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'start_date',
        'end_date',
        'canceled_at',
        'price',
        'currency',
        'type'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
