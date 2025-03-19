@extends('layout.master')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">All Donors</h2>
        <div class="card shadow">
            <div class="card-body">
                <table id="dataTableExample" class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th>No#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Active Donations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donors as $donor)
                            <tr>
                               <td>{{ $loop->iteration }}</td>
                                <td>{{ $donor->first_name }} {{ $donor->last_name }}</td>
                                <td>{{ $donor->email }}</td>

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

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
