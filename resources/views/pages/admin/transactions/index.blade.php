@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">All Transactions</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>User Email</th>
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
                                    <td>{{ $transaction->user->email }}</td>

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
    </div>
@endsection
