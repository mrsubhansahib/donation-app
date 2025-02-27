@extends('layout.master')

@section('content')
<div class="container mt-4">
    <h2>Transaction Details for {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID:</th>
                    <td>{{ $transaction->id }}</td>
                </tr>
                <tr>
                    <th>Amount:</th>
                    <td>{{ $transaction->donation->amount }} {{ $transaction->donation->currency }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span class="badge 
                            {{ $transaction->status == 'successful' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Date:</th>
                    <td>{{ $transaction->created_at }}</td>
                </tr>
            </table>
        </div>
    </div>

    <a href="{{ auth()->user()->role == 'admin' ? route('admin.transactions.index') : route('user.transactions.index') }}" class="btn btn-secondary mt-3">
        Back to Transactions
    </a>
</div>
@endsection
