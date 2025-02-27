@extends('layout.master')

@section('content')
<div class="container mt-4">
    <h2>Donation Details for {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID:</th>
                    <td>{{ $subscription->id }}</td>
                </tr>
                <tr>
                    <th>Plan Name:</th>
                    <td>{{ $subscription->plan_name }}</td>
                </tr>
                <tr>
                    <th>Amount:</th>
                    <td>{{ $subscription->amount }} {{ $subscription->currency }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span class="badge ">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Start Date:</th>
                    <td>{{ $subscription->start_date}}</td>
                </tr>
                <tr>
                    <th>End Date:</th>
                    <td>{{ $subscription->end_date ? $subscription->end_date : 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <a href="{{ auth()->user()->role == 'admin' ? route('admin.subscriptions.index') : route('user.subscriptions.index') }}" class="btn btn-secondary mt-3">
        Back to Donations
    </a>
</div>
@endsection
