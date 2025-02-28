<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use srtipe\invoice\invoice;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Stripe\Charge;
use Stripe\Invoice;

class TransactionController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntents = PaymentIntent::all(['limit' => 50]);

        $transactions = [];

        foreach ($paymentIntents->data as $paymentIntent) {
            $charges = Charge::all(['payment_intent' => $paymentIntent->id]);
        
            foreach ($charges->data as $charge) {
                $invoice = Invoice::retrieve($charge->invoice);

                $email = $invoice->customer_email ?? 'N/A';
                if($email && User::where('email', $email)->exists())
                {
                    $name = User::where('email', $email)->first()->name;
                }else{
                    $name = 'N/A';
                }
                
                $transactions[] = [
                    'name'   => $name,
                    'email'  => $email,
                    'amount' => $charge->amount / 100,
                    'date'   => date('Y-m-d H:i:s', $charge->created),
                    'status' => $charge->status
                ];
            }
        }
        dd($transactions);
    }
}
