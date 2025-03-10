<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Stripe;

class FetchInvoices extends Command
{
    protected $signature = 'fetch:invoices';
    protected $description = 'Fetch invoices from Stripe and save them to the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripeInvoices = \Stripe\Invoice::all(['limit' => 1000]);

        foreach ($stripeInvoices->autoPagingIterator() as $stripeInvoice) {
            $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

            if ($subscription && !Invoice::where('stripe_invoice_id', $stripeInvoice->id)->exists()&&$stripeInvoice->amount_due>0) {
                Invoice::create([
                    'subscription_id' => $subscription->id,
                    'stripe_invoice_id' => $stripeInvoice->id,
                    'amount' => $stripeInvoice->amount_due,
                    'invoice_date' => Carbon::createFromTimestamp($stripeInvoice->created),
                    'status' => $stripeInvoice->status,
                ]);
            }
            if ($subscription && Invoice::where('stripe_invoice_id', $stripeInvoice->id)->exists()&&$stripeInvoice->amount_due>0&&$stripeInvoice->status=='paid') {
                Invoice::where('stripe_invoice_id', $stripeInvoice->id)->update([
                    'status' => $stripeInvoice->status,
                ]);
            }
        }

        $this->info('Invoices fetched successfully!');
    }
}
