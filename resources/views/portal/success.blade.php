<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Application Submitted</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm">
            <div class="card-body text-center">
              <div class="mb-4">
                <span class="badge bg-success px-3 py-2 mb-3">Payment Successful</span>
                <h2 class="fw-bold">Your application has been submitted</h2>
                <p class="text-muted mb-0">
                  Thank you. Your payment was processed successfully and your request is now in the review queue.
                </p>
              </div>
              @isset($service)
                <p class="mb-1"><strong>Service:</strong> {{ $service->name }}</p>
              @endisset
              @isset($reg)
                <p class="mb-1"><strong>Reference:</strong> IPAMS-REQ-{{ str_pad($reg->id, 6, '0', STR_PAD_LEFT) }}</p>
              @endisset
              @isset($payment)
                <p class="mb-3"><strong>Receipt No:</strong> {{ $payment->receipt_number }}</p>
              @endisset
              @isset($receiptUrl)
                <a href="{{ $receiptUrl }}" class="btn btn-outline-primary mb-3" target="_blank" rel="noopener">
                  View/Download Receipt
                </a>
              @endisset
              <div class="mt-3">
                <a href="{{ route('landing.page.index') }}" class="btn btn-primary">Back to Services</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>

