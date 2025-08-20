@extends('layout.master2')

@section('content')
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong>
    @foreach ($errors->all() as $error)
    {{ $error }}
    @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="page-content d-flex align-items-center justify-content-center">
    <div class="row w-100 mx-0 auth-page">
        <div class="col-md-9 col-xl-7 mx-auto">
            <div class="card">
                <div class="row">
                    <div class="col ps-md-0">
                        <div class="auth-form-wrapper px-5 py-5">
                            <a href="#" class="noble-ui-logo d-block mb-2">Donation<span>App</span></a>
                            <h5 class="text-muted fw-normal mb-4">Create a free account.</h5>

                            <form class="row g-3" action="{{ route('register-attempt') }}" method="POST">
                                @csrf

                                <div class="col-md-6">
                                    <label for="title" class="form-label">Title</label>
                                    <select name="title" required id="title" class="form-control">
                                        <option selected value="mr">Mr</option>
                                        <option value="mrs">Mrs</option>
                                        <option value="miss">Miss</option>
                                        <option value="dr">Dr</option>
                                        <option value="prof">Prof</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" required name="first_name"
                                        value="{{ old('first_name') }}" id="first_name" placeholder="First Name">
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" required name="last_name"
                                        value="{{ old('last_name') }}" id="last_name" placeholder="Last Name">
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" required name="email"
                                         id="email" placeholder="Email">
                                </div>

                                <div class="col-md-6">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" required name="country"
                                        value="{{ old('country') }}" id="country" placeholder="Country">
                                </div>

                                <div class="col-md-6">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" required name="city"
                                        value="{{ old('city') }}" id="city" placeholder="City">
                                </div>

                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" required name="address"
                                        value="{{ old('address') }}" id="address" placeholder="Address">
                                </div>

                                <div class="col-md-6">
                                    <label for="zip_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" required name="zip_code"
                                        value="{{ old('zip_code') }}" id="zip_code" placeholder="Postal Code">
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" required name="password"
                                        id="password" placeholder="Password">
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" required name="password_confirmation"
                                        id="password_confirmation" placeholder="Confirm Password">
                                </div>

                                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary">Sign Up</button>
                                    <a href="{{ route('home') }}" class="btn btn-dark">
                                        <i class="me-2 icon-md" data-feather="home"></i> Back to Home
                                    </a>
                                </div>

                                <div class="col-12">
                                    <a href="{{ url('/auth/login') }}" class="d-block mt-3 text-muted">
                                        Already a user? Sign in
                                    </a>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection