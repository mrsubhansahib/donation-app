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
      

        DB::beginTransaction();
        try {


            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!auth()->user()->stripe_id) {
                $customer = Stripe\Customer::create([
                    'email' => $request->email,
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


            $days = Carbon::parse($request->cancellation)->diffInDays($request->start_date);
            $weeks = Carbon::parse($request->cancellation)->diffInWeeks($request->start_date);
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = match ($request->type) {
                'day' => $startDate->copy()->addDays($days)->startOfDay(),
                'week' => $startDate->copy()->addWeeks($weeks)->startOfDay(),
                default => $startDate->copy()->addMonth()->startOfDay(),
            };
            $billingAnchor = $startDate->isPast() ? Carbon::now()->addMinute()->timestamp : $startDate->timestamp;

            $subscription = Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'billing_cycle_anchor' => $billingAnchor,
                'cancel_at' => $endDate->timestamp, // Ensures last invoice is charged
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
                'end_date' => $endDate,
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
