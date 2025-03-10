@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Donor Details</h2>

        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Donor Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Name:</th>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Country:</th>
                        <td>{{ $user->country }}</td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td>{{ $user->city }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Donations</h4>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Cancel Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($user->subscriptions->count() > 0)
                            @foreach ($user->subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td>
                                        @if ($subscription->type == 'day')
                                            Daily
                                        @elseif($subscription->type == 'week')
                                            Weekly
                                        @elseif($subscription->type == 'month')
                                            Monthly
                                        @endif
                                    </td>
                                    <td>
                                        @if ($subscription->currency == 'usd')
                                            {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} $
                                        @elseif($subscription->currency == 'gbp')
                                            {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} £
                                        @elseif($subscription->currency == 'eur')
                                            {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} €
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($subscription->status) }}</td>
                                    <td>{{  \Carbon\Carbon::parse($subscription->start_date)->format('d-m-Y')  }}</td>
                                    <td>{{  \Carbon\Carbon::parse($subscription->end_date)->format('d-m-Y') ?? 'N/A'  }}</td>
                                    <td>{{  \Carbon\Carbon::parse($subscription->cancel_at)->format('d-m-Y') ?? 'N/A'  }}</td>
                                    <td><a href="#" onclick="alert('Coming Soon!')"
                                            class="btn btn-info btn-sm">View</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">

                                    <p class="text-center">No donations found.</p>
                                </td>

                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Invoices</h4>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Invoice Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($invoices->count() > 0)
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>
                                        @if ($invoice->subscription->type == 'day')
                                            Daily
                                        @elseif($invoice->subscription->type == 'week')
                                            Weekly
                                        @elseif($invoice->subscription->type == 'month')
                                            Monthly
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->subscription->currency == 'usd')
                                            {{ $invoice->amount / 100 }} $
                                        @elseif($invoice->subscription->currency == 'gbp')
                                            {{ $invoice->amount / 100 }} £
                                        @elseif($invoice->subscription->currency == 'eur')
                                            {{ $invoice->amount / 100 }} €
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($invoice->status) }}</td>
                                    <td>{{  \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y H:i')  }}</td>
                                    <td><a href="#" onclick="alert('Coming Soon!')"
                                            class="btn btn-info btn-sm">View</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">

                                    <p class="text-center">No invoices found.</p>
                                </td>

                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card shadow">
            <div class="card-body">
                <h4 class="mb-3">Transactions</h4>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($transactions->count() > 0)
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if ($transaction->invoice->subscription->type == 'day')
                                            Daily
                                        @elseif($transaction->invoice->subscription->type == 'week')
                                            Weekly
                                        @elseif($transaction->invoice->subscription->type == 'month')
                                            Monthly
                                        @endif
                                    </td>
                                    <td>
                                        @if ($transaction->invoice->subscription->currency == 'usd')
                                            {{ $transaction->invoice->amount / 100}} $
                                        @elseif($transaction->invoice->subscription->currency == 'gbp')
                                            {{ $transaction->invoice->amount / 100}} £
                                        @elseif($transaction->invoice->subscription->currency == 'eur')
                                            {{ $transaction->invoice->amount / 100}} €
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($transaction->status) }}</td>
                                    <td>{{  \Carbon\Carbon::parse($transaction->paid_at)->format('d-m-Y H:i')  }}</td>
                                    <td><a href="#" onclick="alert('Coming Soon!')"
                                            class="btn btn-info btn-sm">View</a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">

                                    <p class="text-center">No transactions found.</p>
                                </td>

                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ auth()->user()->role == 'admin' ? route('admin.donors.index') : route('admin.donors.dashboard') }}"
            class="btn btn-secondary mt-3">
            Back to Users
        </a>
    </div>
@endsection
