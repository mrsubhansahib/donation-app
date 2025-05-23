@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <div class="d-flex align-items-baseline">

            <ol class="breadcrumb" style="font-size: 17px; font-weight: bold;">
                <li class="breadcrumb-item"><a href="#">Transactions</a></li>
                <li class="breadcrumb-item active" aria-current="page">List</li>
            </ol>
        </div>

    </nav>
    <div class="container ">
        <div class="card shadow">
            <div class="card-body">
                <table id="dataTableExample" class="table">
                    <thead class="thead-dark">
                        <tr>
                            {{-- <th>No#</th> --}}
                            <th>Email</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                               {{-- <td>{{ $loop->iteration }}</td> --}}
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
                                        $ {{ number_format($transaction->invoice->amount / 100, 2) }} 
                                    @elseif($transaction->invoice->subscription->currency == 'gbp')
                                        £ {{ number_format($transaction->invoice->amount / 100, 2) }} 
                                    @elseif($transaction->invoice->subscription->currency == 'eur')
                                        € {{ number_format($transaction->invoice->amount / 100, 2) }} 
                                    @endif
                                </td>
                                <td><span class="badge bg-{{ $transaction->status === 'succeeded' ? 'success' : 'danger' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span></td>
                                <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('d-m-Y H:i') }}</td>
                                <td>
                                    {{-- 
                                        <a href="{{ route('user.transactions.show', $transaction->id) }}"
                                            class="btn btn-info btn-sm">View</a> --}}
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
