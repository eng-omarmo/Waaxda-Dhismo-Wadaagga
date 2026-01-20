<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow-sm">
            <div class="card-body">
              @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
              @endif
              @if($errors->any())
              <div class="alert alert-danger">
                <div class="mb-2">There was a problem initializing the payment:</div>
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
              <div class="mb-4">
                <div class="d-flex justify-content-between">
                  @php
                    $isPermit = ($reg->service_slug === 'construction-permit-application');
                    $stepLabel = $isPermit ? 'Step 2' : 'Step 5';
                    $ofLabel = $isPermit ? 'of 3' : 'of 6';
                    $progressPct = $isPermit ? 66 : 83;
                  @endphp
                  <div><strong>{{ $stepLabel }}</strong> {{ $ofLabel }}</div>
                  <div class="text-muted">Initialize payment</div>
                </div>
                <div class="progress mt-2"><div class="progress-bar" style="width: {{ $progressPct }}%"></div></div>
              </div>
              <div class="alert alert-info">Amount due: ${{ number_format($service->price,2) }}</div>
              <form method="post" action="{{ route('portal.pay.store') }}">
                @csrf
                <input type="hidden" name="payment_method" value="initialize">
                <p class="text-muted mb-3">Click Initialize to create a payment request. You can complete service forms now; payment will be processed later.</p>
                <div class="d-flex justify-content-between">
                  <a href="{{ route('portal.docs') }}" class="btn btn-outline-secondary">Back</a>
                  <button class="btn btn-primary">Initialize</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
