@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <div class="d-flex align-items-baseline">

            <ol class="breadcrumb" style="font-size: 17px; font-weight: bold;">
                <li class="breadcrumb-item"><a href="#">Donors</a></li>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Active Donations</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donors as $donor)
                            <tr>
                                {{-- <td>{{ $loop->iteration }}</td> --}}
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
