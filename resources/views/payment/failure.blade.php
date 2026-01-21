<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Failed</title>
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
              <h2 class="h5 mb-3">{{ $title ?? 'Payment Failed' }}</h2>
              <div class="alert alert-danger">
                {{ $message ?? 'Your payment could not be completed.' }}
              </div>
              @if(!empty($errors))
              <ul class="text-muted">
                @foreach($errors as $err)
                <li>{{ $err }}</li>
                @endforeach
              </ul>
              @endif
              <div class="mb-3">
                <p class="mb-1">Troubleshooting tips:</p>
                <ul class="small text-muted">
                  <li>Verify your phone number format (E.164).</li>
                  <li>Ensure sufficient balance and network connectivity.</li>
                  <li>Retry the payment after a few minutes.</li>
                </ul>
              </div>
              <div class="mb-3">
                @if(!empty($support))
                  <div class="alert alert-info">
                    {{ $support }}
                  </div>
                @endif
              </div>
              <div class="d-flex justify-content-between">
                <a href="{{ ($homeUrl ?? route('landing.page.index')) }}" class="btn btn-outline-secondary">Return to Services</a>
                <a href="{{ ($retryUrl ?? route('portal.pay')) }}" class="btn btn-primary">Retry Payment</a>
                @if(!empty($supportMailTo))
                <a href="{{ $supportMailTo }}" class="btn btn-outline-primary">Contact Support</a>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
        </div>
      </div>
    </div>
  </body>
</html>
