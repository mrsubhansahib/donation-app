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
                        <th>Amount</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th>Attempt Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->donation->amount }}</td>
                            <td>{{ $transaction->donation->currency }}</td>
                            <td>
                                <span class="badge 
                                    {{ $transaction->status == 'successful' ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($transaction->attempt_date)->format('Y-m-d') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
