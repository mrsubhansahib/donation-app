<!DOCTYPE html>
<html lang="en">
<head>
    <title>Donation App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <div class="container">
        <h1>Confirm Payment</h1>
        <div class="row" style="margin-top: 30px">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default credit-card-box">
                    <div class="panel-heading display-table">
                        <h3 class="panel-title">Payment Details</h3>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('success'))
                            <div class="alert alert-success text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @endif
                        <form id="payment-form" action="{{ route('stripe.post') }}" method="post">
                            @csrf

                            <!-- Additional non-sensitive fields -->
                            <div class="form-group">
                                <label for="name">Name on Card</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" id="amount" name="amount" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="cancellation">Cancellation</label>
                                <input type="date" id="cancellation" name="cancellation" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required>
                            </div>

                            {{-- create select option for dynamic currency dollar euro pound --}}
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select name="currency" id="currency" class="form-control" required>
                                    <option value="usd">USD</option>
                                    <option value="eur">EUR</option>
                                    <option value="gbp">GBP</option>
                                </select>
                            </div>
                            <!-- Stripe Elements Card -->
                            <div class="form-row">
                                <label for="card-element">Credit or debit card</label>
                                <div id="card-element"><!-- Stripe Elements will be inserted here --></div>
                                <div id="card-errors" role="alert"></div>
                            </div>

                            <button class="btn btn-primary btn-lg btn-block mt-4" type="submit">Pay Now </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            event.preventDefault();

            // Create the token for the card details
            const {token, error} = await stripe.createToken(card);

            if (error) {
                // Inform the user if there was an error
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
            } else {
                // Send the token to your server along with additional fields
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
</body>
</html>
