<?php

namespace App\Console\Commands;

use App\Subscription;
use Illuminate\Console\Command;
use Stripe\Stripe;

class UpdateSubsriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to update subscriptions in the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripeSubscriptions = \Stripe\Subscription::all([
            'status' => 'all', // Fetch ALL subscription statuses
            'limit' => 100, // Adjust limit if needed
        ]);
        foreach ($stripeSubscriptions->autoPagingIterator() as $stripeSubscription) {
            // Find local subscription
            $localSubscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

            if ($localSubscription && $localSubscription->status !== $stripeSubscription->status) {
                // Update status only if it's different
                $localSubscription->update([
                    'status' => $stripeSubscription->status,
                    'canceled_at' => ($stripeSubscription->status === 'canceled') ? now() : null
                ]);
            }
        }
        $this->info('Subscriptions Updated successfully!');
    }
}
