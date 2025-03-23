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
        $data = $request->validate([
            'email' => 'required|email',
            'currency' => 'required',
            'amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'cancellation' => 'required|date',
            'title' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'type' => 'required', // daily, weekly, monthly
            'country' => 'required',
            'password' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'address' => 'required',
            'stripeToken' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $user->update([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'title' => $data['title'],
                    'password' => Hash::make($data['password']),
                    'city' => $data['city'],
                    'address' => $data['address'],
                    'zip_code' => $data['zip_code'],
                    'country' => $data['country'],
                ]);
            } else {
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'title' => $data['title'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'city' => $data['city'],
                    'address' => $data['address'],
                    'zip_code' => $data['zip_code'],
                    'country' => $data['country'],
                    'stripe_id' => ''
                ]);
            }

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            if (!$user->stripe_id) {
                $customer = Stripe\Customer::create([
                    'email' => $data['email'],
                    'source' => $data['stripeToken'],
                ]);
                $user->update(['stripe_id' => $customer->id]);
            } else {
                $customer = Stripe\Customer::retrieve($user->stripe_id);
            }

            $product = Stripe\Product::create([
                'name' => 'Custom Subscription',
            ]);

            $price = Stripe\Price::create([
                'unit_amount' => $data['amount'] * 100,
                'currency' => $data['currency'],
                'recurring' => ['interval' => $data['type']],
                'product' => $product->id,
            ]);

            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['cancellation'])->endOfDay(); // Ensure last day charge

            $billingAnchor = $startDate->isPast() ? Carbon::now()->addMinute()->timestamp : $startDate->timestamp;

            $subscription = Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'billing_cycle_anchor' => $billingAnchor,
                'cancel_at' => $endDate->timestamp, // Ensures last invoice is charged
                'proration_behavior' => 'none',
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            $user->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $data['amount'],
                'currency' => $data['currency'],
                'type' => $data['type'],
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date' => $endDate,
                'canceled_at' => $endDate,
            ]);
            DB::commit();
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Donation successful! Your invoice and transaction are generated within just 5 minutes due to high traffic.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
