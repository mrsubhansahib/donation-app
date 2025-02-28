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
            // Check if user already exists
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                // Update existing user
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
                // Create New User
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

            // If user doesn't have Stripe ID, create a customer in Stripe
            if (!$user->stripe_id) {
                $customer = Stripe\Customer::create([
                    'email' => $data['email'],
                    'source' => $data['stripeToken'],
                ]);
                $user->update(['stripe_id' => $customer->id]);
            } else {
                // Retrieve existing Stripe customer
                $customer = Stripe\Customer::retrieve($user->stripe_id);
            }

            // Create Stripe Product
            $product = Stripe\Product::create([
                'name' => 'Custom Subscription',
            ]);

            // Create Stripe Price (amount in cents)
            $price = Stripe\Price::create([
                'unit_amount' => $data['amount'] * 100,
                'currency' => $data['currency'],
                'recurring' => ['interval' => $data['type']], // daily, weekly, monthly
                'product' => $product->id,
            ]);

            // Convert start and cancel dates
            $startDate = Carbon::parse($data['start_date']);
            $cancelAt = Carbon::parse($data['cancellation'])->timestamp;
            $now = Carbon::now();

            // Ensure billing cycle anchor is always in the future
            $billingAnchor = $startDate->isPast() || $startDate->isToday() || $now->diffInHours($startDate, false) <= 6
                ? Carbon::now()->addMinute()->timestamp
                : $startDate->timestamp;

            // Create Stripe Subscription
            $subscription = Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'billing_cycle_anchor' => $billingAnchor,
                'expand' => ['latest_invoice.payment_intent'],
                'cancel_at' => $cancelAt,
            ]);

            // Store Subscription in Database
            $dbSubscription = $user->subscriptions()->create([
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $price->id,
                'status' => $subscription->status,
                'price' => $data['amount'],
                'currency' => $data['currency'],
                'type' => $data['type'],
                'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                'end_date' => $subscription->cancel_at ? Carbon::createFromTimestamp($subscription->cancel_at) : null,
                'canceled_at' => $subscription->cancel_at ? Carbon::createFromTimestamp($subscription->cancel_at) : null,
            ]);
            // dd($dbSubscription);
           
            $invoice = $subscription->latest_invoice;
             
            //  $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            //  $invoice = $stripe->invoices->retrieve($subscription->latest_invoice, [
            //      'expand' => ['payment_intent', 'charge', 'payment_method'],
            //  ]);
     
            //  dd($invoice);
             
            //  dd($invoice);
            // dd($invoice->charge);
            // dd($invoice->collection_method); // "charge_automatically" ya "send_invoice"
             // Store Invoice in Database
            //  dd($invoice->status);
            // dd($invoice->payment_intent ?? 'No Payment Intent Found');
//             $chargeId = $invoice->latest_charge ?? 'No Charge Found';
// dd($chargeId);
// dd($invoice->status);

// dd($invoice->collection_method); 

        // $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        // $invoice = $stripe->invoices->retrieve($subscription->latest_invoice, []);
        // dd($invoice);

            // Store Invoice in Database
            $dbInvoice = $user->invoices()->create([
                'stripe_invoice_id' => $invoice->id,
                'stripe_subscription_id' => $subscription->id,
                'currency' => $data['currency'],
                'type' => $data['type'],
                'amount_due' => $invoice->amount_due / 100,
                'amount_paid' => $invoice->amount_paid / 100,
                'status' => $invoice->status,
                'invoice_date' => Carbon::createFromTimestamp($invoice->created),
            ]);

            // Store Transaction in Database
            // if ($invoice->payment_intent) {
            //     $paymentIntent = Stripe\PaymentIntent::retrieve($invoice->payment_intent);
            //     $user->transactions()->create([
            //         'stripe_payment_id' => $paymentIntent->id,
            //         'amount' => $paymentIntent->amount / 100,
            //         'currency' => $paymentIntent->currency,
            //         'type' => $data['type'],
            //         'status' => $paymentIntent->status,
            //         'paid_at' => Carbon::createFromTimestamp($paymentIntent->created),
            //     ]);
            // }

            // DB::commit();

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Subscription successfully created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
