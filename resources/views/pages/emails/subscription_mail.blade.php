{{-- resources/views/emails/subscription_created.blade.php --}}
<h2>Hello {{ $user->name }},</h2>

<p>Your subscription has been successfully activated!</p>

<p><strong>Plan:</strong> {{ $subscription->plan_name ?? 'N/A' }}</p>
<p><strong>Status:</strong> {{ ucfirst($subscription->status) }}</p>

<p>You can manage your subscription here:</p>
<a href="{{ url('/subscriptions') }}">{{ url('/subscriptions') }}</a>

<p>Thanks,<br>The {{ config('app.name') }} Team</p>
