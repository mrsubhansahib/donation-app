<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'invoice_id', // Local Invoice ID
        'stripe_payment_id',
        'paid_at',
        'status', // Paid, Failed, Refunded
    ];


    /**
     * Get the invoice associated with this transaction.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
