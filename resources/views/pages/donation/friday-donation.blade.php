<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Donation App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container  mt-3 w-75">
        <h1 class="text-center">Friday Giving</h1>
        <div class="w-25 m-auto">
            <hr>
        </div>


        <form action="{{route('friday.donation')}}" method="POST" class="mb-5">
            @csrf
            <h3 class="mt-3">I would like to give</h3>
            <div class="row border-2 borderd-dark border p-5">

                <div class="col-md-4">
                    <label for="currency">Currency</label>
                    <select name="currency" id="currency" class="form-control">
                        <option selected value="pound">£</option>
                        <option value="dollar">$</option>
                        <option value="euro">€</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="form-control">
                        <option selected value="friday">Friday</option>
                    </select>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo date('Y-m-d'); ?>" id="start_date"
                        class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control"
                        value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" required>
                </div>
            </div>
            <h3 class="mt-3">Address Details</h3>
            <div class="row border-2 borderd-dark border p-5">
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
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-md-6 mt-3">
                    <label for="country">Country</label>
                    <input type="text" name="country" id="country" class="form-control" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label for="city">City</label>
                    <input type="text" name="city" id="city" class="form-control" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" required>
                </div>
                <div class="col-md-4 mt-3">
                    <label for="postcode">ZipCode</label>
                    <input type="text" name="postcode" id="postcode" class="form-control" required>
                </div>
                
                <div class="col-md-12 mt-3">
                    
                    <label for="tax_payer" class="d-flex"><input type="checkbox" name="tax_payer" id="tax_payer"  required> &nbsp;&nbsp;&nbsp;Yes, I am a UK tax payer and would like Gift Aid claimed on my donations
                    </label>
                </div>
                
            </div>
            <h3 class="mt-3">Card Details</h3>
            <div class="row border-2 borderd-dark border p-5">
                <div class="col-md-6">
                    <label for="card_number">Card Number</label>
                    <input type="text" name="card_number" id="card_number" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label for="cvv">CVV</label>
                    <input type="text" name="cvv" id="cvv" class="form-control" required>
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
</body>

</html>
