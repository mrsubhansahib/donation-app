@extends('layout.master')

@section('content')
<div class="container mt-4">
    <h2>Invoice Details for {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID:</th>
                    <td>{{ $invoice->id }}</td>
                </tr>
                <tr>
                    <th>Amount:</th>
                    <td>{{ $invoice->amount }} {{ $invoice->currency }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span class="badge ">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Issue Date:</th>
                    <td>{{ $invoice->issue_date }}</td>
                </tr>
                <tr>
                    <th>Due Date:</th>
                    <td>{{ $invoice->due_date ? $invoice->due_date : 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <a href="{{ auth()->user()->role == 'admin' ? route('admin.invoices.index') : route('user.invoices.index') }}" class="btn btn-secondary mt-3">
        Back to Invoices
    </a>
</div>
@endsection
