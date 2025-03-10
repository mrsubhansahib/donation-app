@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">All Transactions</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Id</th>
                            <th>Email</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (count($transactions) > 0)
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->invoice->subscription->user->email ?? 'N/A' }}</td>
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
                                            {{ $transaction->invoice->amount / 100 }} $
                                        @elseif($transaction->invoice->subscription->currency == 'gbp')
                                            {{ $transaction->invoice->amount / 100 }} £
                                        @elseif($transaction->invoice->subscription->currency == 'eur')
                                            {{ $transaction->invoice->amount / 100 }} €
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge bg-{{ $transaction->status === 'succeeded' ? 'success' : 'danger' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span></td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('d-m-Y') }}</td>
                                    <td>
                                        {{-- 
                                        <a href="{{ route('user.transactions.show', $transaction->id) }}"
                                            class="btn btn-info btn-sm">View</a> --}}
                                        <a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">

                                    <p class="text-center">No transactions found.</p>
                                </td>

                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
