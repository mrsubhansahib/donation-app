<?php

namespace App\Http\Controllers;

use App\Invoice as AppInvoice;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
// use srtipe\invoice\invoice;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Stripe\Invoice;

class TransactionController extends Controller
{
    public function index()
    {
        // Stripe::setApiKey(env('STRIPE_SECRET'));

        // $paymentIntents = PaymentIntent::all(['limit' => 1000]);

        // foreach ($paymentIntents->data as $paymentIntent) {
        //     $charges = Charge::all(['payment_intent' => $paymentIntent->id]);
        
        //     foreach ($charges->data as $charge) {
        //         if (!$charge->invoice) {
        //             Log::warning("⚠️ Skipping charge with no invoice: {$charge->id}");
        //             continue;
        //         }
        
        //         $invoice = Invoice::retrieve($charge->invoice);
        
        //         if (!$invoice) {
        //             Log::error("❌ No invoice found for charge ID: {$charge->id}");
        //             continue;
        //         }
        
        //         $email = $invoice->customer_email ?? 'N/A';
        //         $user = User::where('email', $email)->first();
        //         $appInvoice = AppInvoice::where('stripe_invoice_id', $invoice->id)->first();
        
        //         if (!$appInvoice) {
        //             Log::warning("⚠️ No matching AppInvoice found for Stripe Invoice ID: {$invoice->id}");
        //             continue;
        //         }
        
        //         if (!Transaction::where('stripe_payment_id', $charge->id)->exists()) {
        //             Transaction::create([
        //                 'invoice_id' => $appInvoice->id,
        //                 'stripe_payment_id' => $charge->id,
        //                 'status'            => $charge->status,
        //                 'paid_at'           => Carbon::createFromTimestamp($charge->created),
        //             ]);
        //         }
        //     }
        // }
        

        $user = auth()->user();

        if ($user->role == 'admin') {
            $transactions = Transaction::all();
            return view('pages.admin.transactions.index', compact('transactions'));
        } else {
            // User sees only their invoices
            $transactions = Transaction::whereHas('invoice.subscription', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->latest()->get();
            return view('pages.user.transactions.index', compact('transactions'));
        }
    }
}
