<?php



namespace App\Http\Controllers;

use App\Card;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Stripe;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Product;
use Stripe\Subscription;

class StripePaymentController extends Controller

{

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function stripe()

    {

        return view('pages.stripe');
    }



    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function stripePost(Request $request)
    {
        // dd($request->all());

        DB::beginTransaction();
        try {


            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_id) {
                $customer = Stripe\Customer::create([
                    'name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'email' => auth()->user()->email,
                    'source' => $request->stripeToken,
                ]);
                auth()->user()->update(['stripe_id' => $customer->id]);
            } else {
                $customer = Stripe\Customer::retrieve(auth()->user()->stripe_id);
            }

            $product = Stripe\Product::create([
                'name' => 'Custom Subscription',
            ]);

            $price = Stripe\Price::create([
                'unit_amount' => $request->amount * 100,
                'currency' => $request->currency,
                'recurring' => ['interval' => $request->type],
                'product' => $product->id,
            ]);

            // Durations from user's requested dates
            $days   = Carbon::parse($request->cancellation)->diffInDays($request->start_date);
            $weeks  = Carbon::parse($request->cancellation)->diffInWeeks($request->start_date);
            $months = Carbon::parse($request->cancellation)->diffInMonths($request->start_date);

            // Requested start (keep for display/storage)
            $startDate = Carbon::parse($request->start_date)->startOfDay();

            // Actual anchor Stripe will use
            $billingAnchor = $startDate->isPast()
                ? Carbon::now()->addMinutes(2)->timestamp
                : $startDate->timestamp;

            // Use the *actual* anchor time-of-day, NO startOfDay() here
            $anchorCarbon = Carbon::createFromTimestamp($billingAnchor);

            // Inclusive iterations (e.g., 4→10 has 7 daily charges)
            $dayIterations   = $days + 1;
            $weekIterations  = $weeks + 1;
            $monthIterations = max(1, ($months ?: 0) + 1);

            // Compute the next boundary at the same time-of-day as the anchor
            $endDate = match ($request->type) {
                'day'   => $anchorCarbon->copy()->addDays($dayIterations),
                'week'  => $anchorCarbon->copy()->addWeeks($weekIterations),
                default => $anchorCarbon->copy()->addMonthsNoOverflow($monthIterations),
            };

            $subscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'billing_cycle_anchor' => $billingAnchor,
                'cancel_at' => $endDate->timestamp,   // cancels at next boundary → full final day charged
                'proration_behavior' => 'none',
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => $request->type,
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date'   => $endDate->copy()->subSecond(),  // last moment before Stripe cancels
                'canceled_at' => $endDate,

            ]);
            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Donation successful! Your invoice and transaction are generated within just 5 minutes due to high traffic.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
