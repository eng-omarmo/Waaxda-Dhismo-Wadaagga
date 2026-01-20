<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-7">
          <div class="card shadow-sm">
            <div class="card-body">
              <h2 class="h5 mb-3">Payment Successful</h2>
              <div class="alert alert-success">
                Your payment was successful. You can continue filling your service details.
              </div>
              <div class="d-flex justify-content-end">
                <a href="{{ $next ?? route('portal.details') }}" class="btn btn-primary">Continue</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
