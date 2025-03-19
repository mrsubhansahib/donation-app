@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    @include('layout.messages')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Welcome to Dashboard</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-12 stretch-card">
            <div class="row flex-grow-1">
                @if (auth()->user()->role == 'admin')
                    <!-- Admin Dashboard Stats -->
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">Total Donors</h6>
                                    <a href="{{ route('admin.donors.index') }}" class="btn btn-sm btn-primary">View</a>
                                </div>
                                <h3 class="mb-2 ms-4">{{ $total_donors }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">Total Donations</h6>
                                    <a href="{{ route('admin.subscriptions.index') }}"
                                        class="btn btn-sm btn-primary">View</a>
                                </div>
                                <h3 class="mb-2 ms-4">{{ $total_donations }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">Total Invoices</h6>
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-primary">View</a>
                                </div>
                                <h3 class="mb-2 ms-4">{{ $total_invoices }}</h3>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- User Dashboard Stats -->
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">My Donations</h6>
                                    <a href="{{ route('user.subscriptions.index') }}"
                                        class="btn btn-sm btn-primary">View</a>
                                </div>
                                <h3 class="mb-2 ms-4">{{ auth()->user()->subscriptions()->count() }}</h3>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">My Invoices</h6>
                                    <a href="{{ route('user.invoices.index') }}" class="btn btn-sm btn-primary">View</a>
                                </div>
                                <h3 class="mb-2 ms-4">{{ auth()->user()->invoices()->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <h6 class="card-title mb-0">My Transactions</h6>
                                    <a href="{{ route('user.transactions.index') }}"
                                        class="btn btn-sm btn-primary">View</a>
                                </div>
                                <h3 class="mb-2 ms-4">{{ count($my_transactions) }}</h3>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush
