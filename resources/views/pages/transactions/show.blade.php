@extends('layout.master')

@section('content')
    <h2>Transactions for {{ $user->first_name }} {{ $user->last_name }}</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->donation->amount }} {{ $transaction->donation->currency }}</td>
                <td>{{ $transaction->status }}</td>
                <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
    </table>
    <a href="{{ route('admin.transactions.index') }}">Back to Transactions</a>
@endsection