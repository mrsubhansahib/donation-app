@extends('layout.master')
@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Your Donations</h2>
        <div class="card shadow">
            <div class="card-body">
                <table id="dataTableExample" class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>No#</th>
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
                                <td>{{ $loop->iteration }}</td>
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
                                        {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} $
                                    @elseif($subscription->currency == 'gbp')
                                        {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} £
                                    @elseif($subscription->currency == 'eur')
                                        {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} €
                                    @endif
                                </td>
                                <td>
                                    {{ ucfirst($subscription->status) }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d-m-Y') }}</td>
                                <td>
                                    {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('d-m-Y') : 'N/A' }}
                                </td>
                                <td>
                                    {{-- <a href="{{ route('user.subscriptions.show', $subscription->id) }}" class="btn btn-info btn-sm">View</a> --}}
                                    @if ($subscription->status == 'active')
                                        <a href="{{ route('user.subscriptions.cancel', $subscription->stripe_subscription_id) }}"
                                            class="btn btn-danger btn-sm">Cancel</a>
                                    @else
                                        <a href="#" class="btn btn-danger disabled btn-sm">Cancel</a>
                                    @endif
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