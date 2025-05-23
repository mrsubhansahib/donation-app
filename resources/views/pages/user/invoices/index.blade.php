@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
        <div class="d-flex align-items-baseline">

            <ol class="breadcrumb" style="font-size: 17px; font-weight: bold;">
                <li class="breadcrumb-item"><a href="#">Invoices</a></li>
                <li class="breadcrumb-item active" aria-current="page">List</li>
            </ol>
        </div>

    </nav>
    <div class="container">
        <div class="card shadow">
            <div class="card-body">
                <table id="dataTableExample" class="table">
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
