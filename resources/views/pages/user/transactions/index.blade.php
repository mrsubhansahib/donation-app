@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Your Transactions</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th>Paid At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>

                                <td>
                                    @if ($subscription->type == 'day')
                                        Daily
                                    @elseif($subscription->type == 'week')
                                        Weekly
                                    @elseif($subscription->type == 'month')
                                        Monthly
                                    @endif
                                </td>
                                <td>${{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ strtoupper($transaction->currency) }}</td>
                                <td>
                                    {{ ucfirst($transaction->status) }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d H:i') }}</td>
                                <td>

                                    {{-- <a href="{{ route('user.transactions.show', $transaction->id) }}" class="btn btn-info btn-sm">View</a> --}}
                                    <a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
