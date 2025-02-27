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
        $user->donations()->create([
            'user_id' => $user->id,
            'currency' => $data['currency'],
            'amount' => $data['amount'],
            'start_date' => $data['start_date'],
            'end_date' => $data['cancellation'],
            'type' => $data['type'],

        ]);




        // Card::create([
        //     'user_id' => $user->id,
        //     'card_number' => $data['card_number'],
        //     'expiry' => $data['expiry_date'],
        //     'cvv' => $data['cvv'],
        // ]);

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

        // Convert start and end dates to Unix timestamps
        $startDate = Carbon::parse($request->start_date);
        $cancelAt = Carbon::parse($request->cancellation)->timestamp;
        
        if ($startDate->isPast()) {
            $startDate = Carbon::now()->addMinute(); // Set trial start at least 1 min in the future
        }
        $startTimestamp = $startDate->timestamp;
        // Step 4: Create a Subscription with Delayed Start using "Trial Period"
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [['price' => $price->id]],
            'trial_end' => $startTimestamp, // Delays the charge until the start date
            // 'cancel_at' => $cancelAt,
        ]);
        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Donation successfully registered');
    }
}
