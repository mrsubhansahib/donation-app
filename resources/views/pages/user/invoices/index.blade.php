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
                        <th>Amount Due</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th>Invoice Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td>
                                @if ($invoice->type == 'day')
                                    Daily
                                @elseif($invoice->type == 'week')
                                    Weekly
                                @elseif($invoice->type == 'month')
                                    Monthly
                                @endif
                            </td>
                            <td>
                                @if ($invoice->currency == 'usd')
                                    {{ $invoice->amount_due ? number_format($invoice->amount_due, 2) : '0' }} $
                                @elseif($invoice->currency == 'gbp')
                                    {{ $invoice->amount_due ? number_format($invoice->amount_due, 2) : '0' }} £
                                @elseif($invoice->currency == 'eur')
                                    {{ $invoice->amount_due ? number_format($invoice->amount_due, 2) : '0' }} €
                                @endif
                            </td>
                            <td>
                                @if ($invoice->currency == 'usd')
                                    {{ $invoice->amount_paid ? number_format($invoice->amount_paid, 2) : '0' }} $
                                @elseif($invoice->currency == 'gbp')
                                    {{ $invoice->amount_paid ? number_format($invoice->amount_paid, 2) : '0' }} £
                                @elseif($invoice->currency == 'eur')
                                    {{ $invoice->amount_paid ? number_format($invoice->amount_paid, 2) : '0' }} €
                                @endif
                            </td>
                            <td>
                                    {{ ucfirst($invoice->status) }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d H:i') }}</td>
                            <td>
                                {{-- <a href="{{ route('user.invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">View</a> --}}
                                <a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a>
                                {{-- @if($invoice->status == 'paid' && $invoice->invoice_pdf)
                                    <a href="{{ $invoice->invoice_pdf }}" target="_blank" class="btn btn-primary btn-sm">Download</a>
                                @endif --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
