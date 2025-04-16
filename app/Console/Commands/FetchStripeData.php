<?php

namespace App\Console\Commands;

use App\Invoice as AppInvoice;
use App\Subscription;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class FetchStripeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:stripeData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to fetch data from Stripe API and update local database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripeSubscriptions = \Stripe\Subscription::all(['limit' => 200, 'status' => 'canceled']);
        foreach ($stripeSubscriptions->autoPagingIterator() as $stripeSubscription) {
            // Find local subscription
            $localSubscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

            if ($localSubscription && $localSubscription->status !== 'canceled'  &&  $stripeSubscription->status == 'canceled' ) {
                // Update status only if it's different
                $localSubscription->update([
                    'status' => $stripeSubscription->status,
                    'canceled_at' => ($stripeSubscription->status === 'canceled') ? now() : null
                ]);
            }
        }
        $stripeInvoices = \Stripe\Invoice::all(['limit' => 200]);

        foreach ($stripeInvoices->autoPagingIterator() as $stripeInvoice) {
            $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

            if ($subscription && !AppInvoice::where('stripe_invoice_id', $stripeInvoice->id)->exists() && $stripeInvoice->amount_due > 0) {
                AppInvoice::create([
                    'subscription_id' => $subscription->id,
                    'stripe_invoice_id' => $stripeInvoice->id,
                    'amount' => $stripeInvoice->amount_due,
                    'invoice_date' => Carbon::createFromTimestamp($stripeInvoice->created),
                    'status' => $stripeInvoice->status,
                ]);
            }
            if ($subscription && AppInvoice::where('stripe_invoice_id', $stripeInvoice->id)->exists() && $stripeInvoice->amount_due > 0 && $stripeInvoice->status == 'paid') {
                AppInvoice::where('stripe_invoice_id', $stripeInvoice->id)->update([
                    'status' => $stripeInvoice->status,
                ]);
            }
        }

        $paymentIntents = PaymentIntent::all(['limit' => 200]);

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
        $this->info('Data fetched successfully!');
    }
}
