@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Your Donations</h2>
        <div class="card shadow">
            <div class="card-body">
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
                        @foreach ($subscriptions as $subscription)
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
                                <td>
                                    {{ ucfirst($subscription->status) }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('Y-m-d') }}</td>
                                <td>
                                    {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td>
                                    {{-- <a href="{{ route('user.subscriptions.show', $subscription->id) }}" class="btn btn-info btn-sm">View</a> --}}
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
