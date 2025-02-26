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
                        <th>User</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->id }}</td>
                            <td>{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}</td>
                            <td>{{ $transaction->donation->amount }} {{ $transaction->donation->currency }}</td>
                            <td>
                                <span class="badge 
                                    {{ $transaction->status == 'successful' ? 'badge-success' : 'badge-danger' }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.transactions.show', $transaction->user_id) }}" class="btn btn-primary btn-sm">
                                    View User
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
