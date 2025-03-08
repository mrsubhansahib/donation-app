@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">All Donors</h2>
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            {{-- <th>Total Donations</th> --}}
                            <th>Active Donations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($donors->count() > 0)
                            @foreach ($donors as $donor)
                                <tr>
                                    <td>{{ $donor->id }}</td>
                                    <td>{{ $donor->first_name }} {{ $donor->last_name }}</td>
                                    <td>{{ $donor->email }}</td>
                                    {{-- <td>
                                        {{ $donor->invoices()->sum('amount') / 100}}
                                    </td> --}}
                                    <td>
                                        {{ $donor->subscriptions->where('status', 'active')->count() }}
                                    </td>
                                    <td>
                                        {{-- <a href="#" onclick="alert('Coming Soon!')" class="btn btn-info btn-sm">View</a> --}}
                                      
                                        <a href="{{ route('admin.donors.show', $donor->id) }}"
                                            class="btn btn-info btn-sm">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">

                                    <p class="text-center">No donors found.</p>
                                </td>

                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
