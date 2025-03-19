<?php

namespace App\Console\Commands;

use App\Invoice as AppInvoice;
use Stripe\Invoice;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class FetchTransactions extends Command
{
    protected $signature = 'fetch:transactions';
    protected $description = 'Fetch transactions from Stripe and store them in the database';

    public function handle()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntents = PaymentIntent::all(['limit' => 1000]);

        foreach ($paymentIntents->data as $paymentIntent) {
            $charges = Charge::all(['payment_intent' => $paymentIntent->id]);
        
            foreach ($charges->data as $charge) {
                if (!$charge->invoice) {
                    Log::warning("⚠️ Skipping charge with no invoice: {$charge->id}");
                    continue;
                }
        
                $invoice = Invoice::retrieve($charge->invoice);
        
                if (!$invoice) {
                    Log::error("❌ No invoice found for charge ID: {$charge->id}");
                    continue;
                }
        
                $email = $invoice->customer_email ?? 'N/A';
                $user = User::where('email', $email)->first();
                $appInvoice = AppInvoice::where('stripe_invoice_id', $invoice->id)->first();
        
                if (!$appInvoice) {
                    Log::warning("⚠️ No matching AppInvoice found for Stripe Invoice ID: {$invoice->id}");
                    continue;
                }
        
                if (!Transaction::where('stripe_payment_id', $charge->id)->exists()) {
                    Transaction::create([
                        'invoice_id' => $appInvoice->id,
                        'stripe_payment_id' => $charge->id,
                        'status'            => $charge->status,
                        'paid_at'           => Carbon::createFromTimestamp($charge->created),
                    ]);
                }
            }
        }
        
        $this->info('Transactions fetched and updated successfully.');
    }
}
