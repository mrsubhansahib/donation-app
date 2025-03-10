@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Your Invoices</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Donation Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Invoice Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($invoices->count() > 0)
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
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
                                            {{ number_format($invoice->amount / 100, 2) }} $
                                        @elseif($invoice->subscription->currency == 'gbp')
                                            {{ number_format($invoice->amount / 100, 2) }} £
                                        @elseif($invoice->subscription->currency == 'eur')
                                            {{ number_format($invoice->amount / 100, 2) }} €
                                        @endif
                                    </td>
                                    <td>
                                        {{ ucfirst($invoice->status) }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y H:i') }}</td>
                                    <td>
                                        {{-- <a href="{{ route('user.invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">View</a> --}}
                                        <a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                        {{-- @if ($invoice->status == 'paid' && $invoice->invoice_pdf)
                                    <a href="{{ $invoice->invoice_pdf }}" target="_blank" class="btn btn-primary btn-sm">Download</a>
                                @endif --}}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">

                                    <p class="text-center">No invoices found.</p>
                                </td>

                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
