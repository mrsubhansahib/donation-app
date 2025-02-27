<?php



namespace App\Http\Controllers;

use App\Card;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $data = $request->validate([
            'email' => 'required|email',
            'currency' => 'required',
            'amount' => 'required',
            'start_date' => 'required',
            'cancellation' => 'required',
            'title' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'type' => 'required',
            'country' => 'required',
            'password' => 'required',
            'zip_code' => 'required',
            'city' => 'required',
            'address' => 'required',
        ]);
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
        ]);




        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // Step 1: Create a Product (only if it doesn’t already exist)
        $product = Product::create([
            'name' => 'Custom Subscription',
        ]);

        // Step 2: Create a Dynamic Price for the User
        $price = Price::create([
            'unit_amount' => $request->amount * 100, // Convert to cents
            'currency' => $request->currency, // Make currency dynamic
            'recurring' => ['interval' => $request->type], // Daily, Weekly, Monthly
            'product' => $product->id,
        ]);

        // Step 3: Create a Customer in Stripe
        // dd($request->stripeToken);
        $customer = Customer::create([
            'email' => $request->email, // Make email dynamic
            'source' => $request->stripeToken,
        ]);

        // Convert start and end dates to Carbon instances
        // Convert start and end dates to Carbon instances
        $startDate = Carbon::parse($request->start_date);
        $cancelAt = Carbon::parse($request->cancellation)->timestamp;
        $now = Carbon::now();

        // Debug: See how Carbon interprets the dates
        // dd([
        //     'start_date' => $startDate->toDateTimeString(),
        //     'now' => $now->toDateTimeString(),
        //     'diff_in_hours' => $now->diffInHours($startDate, false),
        //     'is_past' => $startDate->isPast(),
        //     'is_today' => $startDate->isToday(),
        // ]);


        if ($startDate->isPast() || $startDate->isToday() || Carbon::now()->diffInHours($startDate, false) <= 6) {
            // Charge immediately but add a buffer to prevent past timestamp error
            $billingAnchor = Carbon::now()->addMinute()->timestamp; // Add 1 minute buffer

            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'billing_cycle_anchor' => $billingAnchor, // Use future timestamp
                'expand' => ['latest_invoice.payment_intent'], // Fetch payment status immediately
                'cancel_at' => $cancelAt, // Set auto-cancel date
            ]);
        } else {
            // Set trial period (charge after trial ends)
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [['price' => $price->id]],
                'trial_end' => $startDate->timestamp, // Delays charge until future start date
                'cancel_at' => $cancelAt,
            ]);
        }



        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Donation successfully registered');
    }
}
