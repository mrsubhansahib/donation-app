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

            // Window & dates
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $days   = Carbon::parse($request->cancellation)->diffInDays($request->start_date);
            $weeks  = Carbon::parse($request->cancellation)->diffInWeeks($request->start_date);
            $months = Carbon::parse($request->cancellation)->diffInMonths($request->start_date);

            $startIsFuture  = $startDate->isFuture();
            $forceChargeNow = (bool) $request->boolean('charge_now');

            // Anchor ONLY for our own endDate math (Stripe ko na bhejein in immediate path)
            $anchor = $forceChargeNow || !$startIsFuture ? Carbon::now() : $startDate->copy();

            $iterationsDay   = $days + 1;
            $iterationsWeek  = $weeks + 1;
            $iterationsMonth = max(1, ($months ?: 0) + 1);

            $endDate = match ($request->type) {
                'day'   => $anchor->copy()->addDays($iterationsDay),
                'week'  => $anchor->copy()->addWeeks($iterationsWeek),
                default => $anchor->copy()->addMonthsNoOverflow($iterationsMonth),
            };

            if ($forceChargeNow || !$startIsFuture) {
                // ===== IMMEDIATE-CHARGE PATH =====
                // IMPORTANT: Do NOT send billing_cycle_anchor (Stripe will start "now")
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete', // we'll finalize+pay below
                    'expand'             => ['latest_invoice'],
                ]);

                // Always fetch invoice by ID
                $latest   = $subscription->latest_invoice;
                $latestId = is_string($latest) ? $latest : ($latest->id ?? null);
                if (!$latestId) {
                    throw new \Exception('Latest invoice ID not found on subscription (immediate charge path).');
                }
                $invoice = Stripe\Invoice::retrieve($latestId);

                // Finalize if draft
                if ($invoice->status === 'draft') {
                    $invoice = $invoice->finalizeInvoice(); // instance method
                }

                // Pay now if not paid yet
                if ($invoice->collection_method === 'charge_automatically' && $invoice->status !== 'paid') {
                    $invoice = $invoice->pay(); // instance method
                }

            } else {
                // ===== FUTURE START / TRIAL PATH =====
                // No invoice yet; it will be created at trial_end
                $subscription = Stripe\Subscription::create([
                    'customer'           => $customer->id,
                    'items'              => [['price' => $price->id]],
                    'trial_end'          => $startDate->timestamp,   // start & bill on this date
                    'cancel_at'          => $endDate->timestamp,
                    'proration_behavior' => 'none',
                    'collection_method'  => 'charge_automatically',
                    'payment_behavior'   => 'allow_incomplete',
                ]);
            }

            // Save local record
            auth()->user()->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $request->amount,
                'currency' => $request->currency,
                'type' => $request->type,
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date'   => $endDate->copy()->subSecond(),
                'canceled_at' => $endDate,
            ]);
            DB::commit();

            $msg = $forceChargeNow || !$startIsFuture
                ? 'Donation successful! Invoice finalized & paid immediately.'
                : 'Subscription scheduled. Billing will start on your selected start date.';

            return redirect()->route('dashboard')->with('success', $msg);

        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Stripe card error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
