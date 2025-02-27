<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Donation App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>

          body {
            font-family: Arial, sans-serif;
            background-color: #e6ebf1;
        }


        .card-body {
            padding-bottom: 0px;
        }

        .featured-post .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(10, 10, 10, 0.1);
            transition: transform 0.3s ease;
        }

        .post-card {
            /* height: 75vh; */
            border: none;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            cursor: pointer;
        }

        .post-card h5{
            font-size: 25px;
        }

        .post-card img {
            height: 290px;
            object-fit: cover;
        }

        .post-card:hover {
            transform: translateY(-5px); /* Slightly lift the card */
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2), 0 3px 6px rgba(0, 0, 0, 0.1);
        }


        .featured-post .card:hover {
            box-shadow: 10 20px 0px rgba(3, 3, 3, 0.1);
        }

        /* Card Titles */
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }

        .text-area {
            height: 85px;
            padding-inline: 20px;
            font-size: 1rem;
        }

        .card-footer {
            color: #6c757d;
            background-color: white;
            border-top: none;
            font-size: 15px;
        }

        .card-footer button{
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            /* background-color: black !important; */
            color: white;
        }

        .card-footer button:hover{
            background-color: black;
        }
    </style>
</head>

<body>
  <div class="container text-center mt-5">
    <div class="d-flex justify-content-around">
      <div class=""></div>
      <h1 style="font-weight: 800;    margin-bottom: 0;">DONATION FORM</h1>
      <button class="btn px-4  btn-secondary" onclick="window.location.href='{{route('login')}}'">Login</button>
    </div>
    <div class="m-auto" style="width: 5% !important;">
      <hr style="height: 4px;">
    </div>
    <p>Browse our donation form to find a starting point.</p>
  </div>
  
<div class="container">
  <!-- card-1 -->
  <div class="row mt-5">
      <div class="col-md-4">
          <div class="card post-card"  onclick="window.location.href='{{URL::to('/donation/regular')}}';">
              <img src="{{asset('assets/images/donate.jpg')}}" class="card-img-top" alt="Post 1">
              {{-- <div class="card-body">
                  <h5 class="card-title">How to get the most out of VehicleScore</h5>
                  <div class="text-area">
                      <p class="card-text">You've got your creditscore, but have you got your VehicleScore? This blog covers what VehicleScore is, and how it can help you in your search for a new vehicle...</p>
                  </div>
              </div> --}}
              
              <div class="card-footer d-flex justify-content-center">                  
                  <p style="margin: 0;padding: 10px;font-size: 18px;font-weight: 600;">Daily / Weekly / Monthly Giving</p>
              </div>
          </div>
      </div>
      <!-- card-2 -->
      <div class="col-md-4">
          <div class="card post-card"  onclick="alert('Coming Soon!');">
              <img src="{{asset('assets/images/donate.jpg')}}" class="card-img-top" alt="Post 2">
              {{-- <div class="card-body">
                  <h5 class="card-title">SMMT Urges UK Government to Take Action on £106bn Opportunity</h5>
                  <div class="text-area">
                      <p class="card-text">You've got your creditscore, but have you got your VehicleScore? This blog covers what VehicleScore is, and how it can help you in your search for a new vehicle...</p>
                  </div>
              </div> --}}
              
              <div class="card-footer d-flex justify-content-center">
                  <p style="margin: 0;padding: 10px;font-size: 18px;font-weight: 600;">Friday Giving</p>
              </div>
          </div>
      </div>
      <!-- card-3 -->
      <div class="col-md-4">
          <div class="card post-card"  onclick="alert('Coming Soon!');">
              <img src="{{asset('assets/images/donate.jpg')}}" class="card-img-top" alt="Post 3">
              {{-- <div class="card-body">
                  <h5 class="card-title">London ULEZ Explainsion Explained</h5>
                  <div class="text-area">
                      <p class="card-text">You've got your creditscore, but have you got your VehicleScore? This blog covers what VehicleScore is, and how it can help you in your search for a new vehicle...</p>
                  </div>
              </div> --}}
              
              <div class="card-footer d-flex justify-content-center">
                
                <p style="margin: 0;padding: 10px;font-size: 18px;font-weight: 600;">Ramadan Giving</p>
              </div>
          </div>
      </div>
      <!-- Add more posts similarly -->
  </div>
</div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
