<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'stripe_invoice_id',
        'amount',
        'invoice_date',
        'status',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
