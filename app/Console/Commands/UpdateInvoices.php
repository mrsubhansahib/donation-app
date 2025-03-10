<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Stripe;

class UpdateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to update invoices in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripeInvoices = \Stripe\Invoice::all();

        foreach ($stripeInvoices->autoPagingIterator() as $stripeInvoice) {
            $subscription = Subscription::where('stripe_subscription_id', $stripeInvoice->subscription)->first();

            if ($subscription && Invoice::where('stripe_invoice_id', $stripeInvoice->id)->exists()&&$stripeInvoice->amount_due>0&&$stripeInvoice->status=='paid') {
                Invoice::where('stripe_invoice_id', $stripeInvoice->id)->update([
                    'status' => $stripeInvoice->status,
                ]);
            }
        }

        $this->info('Invoices Updated successfully!');
    }
}
