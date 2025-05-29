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
                                <form class="forms-sample row" action="{{ route('register-attempt') }}" method="POST">
                                  @csrf
                                    <div class="mb-3 col-2">
                                        <label for="title" class="form-label">Title</label>
                                        <select name="title" required name="title" value="{{ old('title') }}"
                                            id="title" class="form-control">
                                            <option selected value="mr">Mr</option>
                                            <option value="mrs">Mrs</option>
                                            <option value="miss">Miss</option>
                                            <option value="dr">Dr</option>
                                            <option value="prof">Prof</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-5">
                                        <label for="exampleInputFirstName1" class="form-label">First Name</label>
                                        <input type="text" class="form-control" required name="first_name"
                                            value="{{ old('first_name') }}" id="exampleInputFirstName1"
                                            autocomplete="FirstName" placeholder="First Name">
                                    </div>
                                    <div class="mb-3 col-5">
                                        <label for="exampleInputLastName1" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" required name="last_name"
                                            value="{{ old('last_name') }}" id="exampleInputLastName1" autocomplete="LastName"
                                            placeholder="Last Name">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="userEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" required name="email"
                                            value="{{ old('email') }}" id="userEmail" placeholder="Email">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="userCountry" class="form-label">Country</label>
                                        <input type="text" class="form-control" required name="country"
                                            value="{{ old('country') }}" id="userCountry" placeholder="Country">
                                    </div>
                                    {{-- create 3 more fields for adress city postalcode and give them col-4 to each --}}
                                    <div class="mb-3 col-4">
                                        <label for="userCity" class="form-label">City</label>
                                        <input type="text" class="form-control" required name="city"
                                            value="{{ old('city') }}" id="userCity" placeholder="City">
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label for="userAddress" class="form-label">Address</label>
                                        <input type="text" class="form-control" required name="address"
                                            value="{{ old('address') }}" id="userAddress" placeholder="Address">
                                    </div>
                                    <div class="mb-3 col-4">
                                        <label for="userPostalCode" class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" required name="zip_code"
                                            value="{{ old('zip_code') }}" id="userPostalCode" placeholder="Postal Code">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="userPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" required name="password"
                                       id="userPassword" autocomplete="current-password"
                                            placeholder="Password">
                                    </div>
                                    <div class="mb-3 col-6">
                                        <label for="userPasswordConfirmation" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            id="userPasswordConfirmation" autocomplete="current-password"
                                            placeholder="Password">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Sign Up</button>
                                        <a href="{{ route('home') }}" class="btn btn-dark me-2 mb-2 mb-md-0">
                                            <i class="me-2 icon-md" data-feather="home"></i>
                                            Back to Home
                                        </a>
                                        {{-- <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                                            <i class="btn-icon-prepend" data-feather="twitter"></i>
                                            Sign up with twitter
                                        </button> --}}
                                    </div>
                                    <a href="{{ url('/auth/login') }}" class="d-block mt-3 text-muted">Already a user?
                                        Sign
                                        in</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
