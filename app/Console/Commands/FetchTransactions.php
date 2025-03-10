<?php

namespace App\Console\Commands;

use App\Invoice as AppInvoice;
use Stripe\Invoice;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
                $invoice = $charge->invoice ? Invoice::retrieve($charge->invoice) : null;

                $email = $invoice->customer_email ?? 'N/A';
                $user = User::where('email', $email)->first();
                $invoice = AppInvoice::where('stripe_invoice_id', $invoice->id)->first();
                // Check if transaction already exists
                if ($invoice && !Transaction::where('stripe_payment_id', $charge->id)->exists()) {
                    Transaction::create([
                        'invoice_id' => $invoice->id,
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
