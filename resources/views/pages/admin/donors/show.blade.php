@extends('layout.master')

@section('content')
   <nav class="page-breadcrumb">
        <div class="d-flex align-items-baseline">

            <ol class="breadcrumb" style="font-size: 17px; font-weight: bold;">
                <li class="breadcrumb-item"><a href="#">Donation</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </div>

    </nav>
    <div class="container">

        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Donor Information</h4>
                <table class="table table-bordered">
                    <tr>
                        <th>Name:</th>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Country:</th>
                        <td>{{ $user->country }}</td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td>{{ $user->city }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Donations</h4>
                <table id="" class="table dataTableExample">
                    <thead class="thead-dark">
                        <tr>
                            {{-- <th>No#</th> --}}
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Cancel Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscriptions as $subscription)
                            <tr>
                                {{-- <td>{{ $loop->iteration }}</td> --}}
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
                                       $ {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} 
                                    @elseif($subscription->currency == 'gbp')
                                       £ {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} 
                                    @elseif($subscription->currency == 'eur')
                                       € {{ $subscription->price ? number_format($subscription->price, 2) : '0' }} 
                                    @endif
                                </td>
                                <td>{{ ucfirst($subscription->status) }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d-m-Y') ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->canceled_at)->format('d-m-Y') ?? 'N/A' }}</td>
                                <td><a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h4 class="mb-3">Invoices</h4>
                <table id="" class="table dataTableExample">
                    <thead class="thead-dark">
                        <tr>
                            {{-- <th>No#</th> --}}
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Invoice Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                <td>
                                    @if ($invoice->subscription->type == 'day')
                                        Daily
                                    @elseif($invoice->subscription->type == 'week')
                                        Weekly
                                    @elseif($invoice->subscription->type == 'month')
                                        Monthly
                                    @endif
                                </td>
                                <td>
                                    @if ($invoice->subscription->currency == 'usd')
                                        $ {{ number_format($invoice->amount / 100, 2) }} 
                                    @elseif($invoice->subscription->currency == 'gbp')
                                        £ {{ number_format($invoice->amount / 100, 2) }} 
                                    @elseif($invoice->subscription->currency == 'eur')
                                        € {{ number_format($invoice->amount / 100, 2) }} 
                                    @endif
                                </td>
                                <td>{{ ucfirst($invoice->status) }}</td>
                                <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y H:i') }}</td>
                                <td><a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card shadow">
            <div class="card-body">
                <h4 class="mb-3">Transactions</h4>
                <table id="" class="table dataTableExample">
                    <thead class="thead-dark">
                        <tr>
                            {{-- <th>No#</th> --}}
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Paid At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                {{-- <td>{{ $loop->iteration }}</td> --}}
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
                                <td>{{ ucfirst($transaction->status) }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->paid_at)->format('d-m-Y H:i') }}</td>
                                <td><a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ auth()->user()->role == 'admin' ? route('admin.donors.index') : route('admin.donors.dashboard') }}"
            class="btn btn-secondary mt-3">
            Back to Donors List
        </a>
    </div>
@endsection

@push('custom-scripts')
    <script>
        $(function() {
            'use strict';

            $('.dataTableExample').each(function() { // Target all tables with the class 'dataTableExample'
                var datatable = $(this).DataTable({
                    "aLengthMenu": [
                        [10, 30, 50, -1],
                        [10, 30, 50, "All"]
                    ],
                    'ordering': false,
                    "iDisplayLength": 10,
                    "language": {
                        search: ""
                    }
                });

                // SEARCH - Add placeholder for Search and turn it into in-line form control
                var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Search');
                search_input.removeClass('form-control-sm');

                // LENGTH - Inline-Form control
                var length_sel = $(this).closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
            });

        });
    </script>
@endpush
