@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">All Transactions</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if (count($transactions) > 0)
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->user->email ?? 'N/A' }}</td>
                                    <td>${{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ strtoupper($transaction->currency) }}</td>
                                    <td>{{ $transaction->paid_at}}</td>
                                    <td><span
                                            class="badge bg-{{ $transaction->status === 'succeeded' ? 'success' : 'danger' }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span></td>
                                    <td>
{{-- 
                                        <a href="{{ route('user.transactions.show', $transaction->id) }}"
                                            class="btn btn-info btn-sm">View</a> --}}
                                        <a href="#" onclick="alert('Coming Soon!')"
                                            class="btn btn-info btn-sm">View</a>
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
