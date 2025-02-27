<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'stripe_invoice_id', 'stripe_subscription_id', 'amount_due', 'amount_paid', 'status', 'invoice_date'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
