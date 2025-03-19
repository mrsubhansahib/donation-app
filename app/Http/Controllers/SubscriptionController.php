<?php

namespace App\Http\Controllers;

use App\Subscription;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        

        $user = auth()->user();

        if ($user->role == 'admin') {
            // Admin sees all subscriptions
            $subscriptions = Subscription::with('user')->orderBy('created_at', 'desc')->get();
            return view('pages.admin.subscriptions.index', compact('subscriptions'));
        } else {
            // User sees only their subscriptions
            $subscriptions = Subscription::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            return view('pages.user.subscriptions.index', compact('subscriptions'));
        }
    }

    public function show($id)
    {

        $user = auth()->user();

        if ($user->role == 'admin') {
            $subscriptions = Subscription::all();
            return view('pages.admin.subscriptions.show', compact('subscriptions'));
        } else {
            $subscription = Auth::user()->subscriptions()->findOrFail($id);
            return view('pages.user.subscriptions.show', compact('subscription'));
        }
    }
    public function cancel_subscription($id)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $subscription=StripeSubscription::retrieve($id);
        $subscription->cancel();
        $subscription = Subscription::where('stripe_subscription_id', $id)->first();
        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now()
        ]);
        return redirect()->back()->with('success', 'Subscription canceled successfully');
    }
}
