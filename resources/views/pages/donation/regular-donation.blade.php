<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Donation App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .form-card {
            background: white;
            border-radius: 25px;
        }
    </style>
</head>

<body style="background: #e9eae4;">
    <h1 class="text-center text-light p-5" style="background: #909d97;font-size: 44px;">Daily / Weekly / Monthly
        Donation</h1>
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="container  mt-3 w-75">

        <form action="{{ route('stripe.post') }}" method="POST" id="payment-form" class="mb-5">
            @csrf
            <h2 class="mt-5 mb-2">I would like to give</h2>
            <div class="row border-2 borderd-dark border p-3 pb-4 form-card">

                <div class="col-md-4">
                    <label for="currency">Currency</label>
                    <select name="currency" id="currency" class="form-control">
                        <option selected value="gbp" {{ old('currency') == 'gbp' ? 'selected' : '' }}>£</option>
                        <option value="usd" {{ old('currency') == 'usd' ? 'selected' : '' }}>$</option>
                        <option value="eur" {{ old('currency') == 'eur' ? 'selected' : '' }}>€</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" class="form-control"
                        required>
                </div>
                <div class="col-md-4">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="form-control">
                        <option selected value="day" {{ old('type') == 'day' ? 'selected' : '' }}>Daily</option>
                        <option disabled value="week" {{ old('type') == 'week' ? 'selected' : '' }}>Weekly</option>
                        <option disabled value="month" {{ old('type') == 'month' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                        id="start_date" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="cancellation">End Date</label>
                    <input type="date" name="cancellation" id="cancellation" class="form-control"
                        value="{{ old('cancellation', date('Y-m-d', strtotime('+1 month'))) }}" required>
                </div>
            </div>
            <h2 class="mt-4 mb-2">Address Details</h2>
            <div class="row border-2 borderd-dark border p-3 pb-4 form-card">
                <div class="col-md-2">
                    <label for="title">Title</label>
                    <select name="title" id="title" class="form-control">
                        <option selected value="mr">Mr</option>
                        <option value="mrs">Mrs</option>
                        <option value="miss">Miss</option>
                        <option value="dr">Dr</option>
                        <option value="prof">Prof</option>
                    </select>
                </div>
                <div class="col-md-10">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                        class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control"
                        value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="country">Country</label>
                    <input type="text" name="country" id="country" class="form-control"
                        value="{{ old('country') }}" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control"
                        value="{{ old('city') }}" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control"
                        value="{{ old('address') }}" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label for="zip_code">ZipCode</label>
                    <input type="text" name="zip_code" id="zip_code" class="form-control"
                        value="{{ old('zip_code') }}" required>
                </div>

                <div class="col-md-12 mt-3">

                    <label for="tax_payer" class="d-flex"><input type="checkbox" name="tax_payer" id="tax_payer"
                            > &nbsp;&nbsp;&nbsp;Yes, I am a UK tax payer and would like Gift Aid claimed on my
                        donations
                    </label>
                </div>

            </div>
            <h2 class="mt-4 mb-2">Card Details</h2>
            <div class="row border-2 borderd-dark border p-3 pb-4 pt-5 form-card">
                <!-- Stripe Elements Card -->
                <div class="form-row mb-3">
                    {{-- <label for="card-element">Credit or debit card</label> --}}
                    <div id="card-element"><!-- Stripe Elements will be inserted here --></div>
                    <div id="card-errors" role="alert"></div>
                </div>
                <div class="col-md-12 mt-3 m-auto   w-50">
                    <button type="submit" class="btn btn-md btn-secondary w-100 ">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        const stripe = Stripe("{{ config('services.stripe.key') }}"); // Use Stripe public key from config
        const elements = stripe.elements();

        // Create an instance of the card Element with ZIP code hidden
        const card = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            },
            hidePostalCode: true
        });

        // Add the card Element into the `card-element` <div>
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element
        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault(); // Stop default form submission

            // Disable the submit button to prevent multiple submissions
            document.querySelector("button[type=submit]").disabled = true;

            // Create the token for the card details
            const {
                token,
                error
            } = await stripe.createToken(card);

            if (error) {
                // Show error and re-enable submit button
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                document.querySelector("button[type=submit]").disabled = false;
            } else {
                // Send the token to your server
                stripeTokenHandler(token);
            }
        });

        // Function to submit the form with Stripe token and additional data
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form with the token and other fields
            form.submit();
        }
    </script>

    <script>
        // Function to set the minimum date for both inputs
        function setMinDate() {
            const now = new Date();

            // Adjust for timezone offset to get correct local date
            const localDate = new Date(now.getTime() - now.getTimezoneOffset() * 60000);

            // Ensure we always pick the next valid day
            const today = localDate.toISOString().split('T')[0];
            const tomorrow = new Date(localDate);
            tomorrow.setDate(localDate.getDate() + 1); // Next day

            const oneMonthLater = new Date(localDate);
            oneMonthLater.setMonth(localDate.getMonth() + 1); // One month from today

            const formattedTomorrow = tomorrow.toISOString().split('T')[0];
            const formattedOneMonthLater = oneMonthLater.toISOString().split('T')[0];

            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('cancellation');

            // Set min values
            startDateInput.min = today; // Set minimum date to today
            endDateInput.min = formattedTomorrow; // Ensure end date is at least tomorrow

            // Set default values
            startDateInput.value = today;
            endDateInput.value = formattedOneMonthLater;
        }

        // Call the function when the page loads
        window.onload = setMinDate;
    </script>

</body>

</html>
