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
  <div class="container text-center mt-3">
    <h1>DONATION FORM</h1>
    <div class="w-25 m-auto">
      <hr>
    </div>
    <p>Browse our donation form to find a starting point.</p>
  </div>
  <div class="container mt-5 d-flex justify-content-around">
    <button style="padding: 15px 90px;" onclick="window.location.href='{{URL::to('/donation/regular')}}';">Daily | Weekly | Monthly  Donation</button>
    {{-- <button style="padding: 15px 90px;" onclick="window.location.href='{{URL::to('/donation/friday')}}';">Friday  Giving</button> --}}
    {{-- <button style="padding: 15px 90px;" onclick="window.location.href='{{URL::to('/donation/ramadan')}}';">Ramadan Giving </button> --}}
    
  </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
