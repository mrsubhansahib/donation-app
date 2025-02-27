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
                                            {{ $subscription->amount ? number_format($subscription->amount, 2) : '0' }} $
                                        @elseif($subscription->currency == 'gbp')
                                            {{ $subscription->amount ? number_format($subscription->amount, 2) : '0' }} £
                                        @elseif($subscription->currency == 'eur')
                                            {{ $subscription->amount ? number_format($subscription->amount, 2) : '0' }} €
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($subscription->status) }}</td>
                                    <td>{{ $subscription->start_date }}</td>
                                    <td>{{ $subscription->end_date ?? 'N/A' }}</td>
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

                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Status</th>
                            <th>Invoice Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($user->invoices->count() > 0)
                            @foreach ($user->invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>
                                        @if ($invoice->type == 'day')
                                            Daily
                                        @elseif($invoice->type == 'week')
                                            Weekly
                                        @elseif($invoice->type == 'month')
                                            Monthly
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->currency == 'usd')
                                            {{ $invoice->amount_due ? number_format($invoice->amount_due, 2) : '0' }} $
                                        @elseif($invoice->currency == 'gbp')
                                            {{ $invoice->amount_due ? number_format($invoice->amount_due, 2) : '0' }} £
                                        @elseif($invoice->currency == 'eur')
                                            {{ $invoice->amount_due ? number_format($invoice->amount_due, 2) : '0' }} €
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->currency == 'usd')
                                            {{ $invoice->amount_paid ? number_format($invoice->amount_paid, 2) : '0' }} $
                                        @elseif($invoice->currency == 'gbp')
                                            {{ $invoice->amount_paid ? number_format($invoice->amount_paid, 2) : '0' }} £
                                        @elseif($invoice->currency == 'eur')
                                            {{ $invoice->amount_paid ? number_format($invoice->amount_paid, 2) : '0' }} €
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($invoice->status) }}</td>
                                    <td>{{ $invoice->invoice_date }}</td>
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
                        @if ($user->transactions->count() > 0)
                            @foreach ($user->transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if ($transaction->type == 'day')
                                            Daily
                                        @elseif($transaction->type == 'week')
                                            Weekly
                                        @elseif($transaction->type == 'month')
                                            Monthly
                                        @endif
                                    </td>
                                    <td>
                                        @if ($transaction->currency == 'usd')
                                            {{ $transaction->amount ? number_format($transaction->amount, 2) : '0' }} $
                                        @elseif($transaction->currency == 'gbp')
                                            {{ $transaction->amount ? number_format($transaction->amount, 2) : '0' }} £
                                        @elseif($transaction->currency == 'eur')
                                            {{ $transaction->amount ? number_format($transaction->amount, 2) : '0' }} €
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($transaction->status) }}</td>
                                    <td>{{ $transaction->paid_at }}</td>
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
