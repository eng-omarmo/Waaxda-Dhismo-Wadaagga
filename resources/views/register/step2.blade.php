<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Secure Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="mb-4">
                <div class="d-flex justify-content-between">
                  <div><strong>Step 2</strong> of 2</div>
                  <div class="text-muted">Payment</div>
                </div>
                <div class="progress mt-2" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar" style="width: 100%"></div>
                </div>
              </div>

              <div class="alert alert-info">Registration Fee: ${{ number_format($amount,2) }}</div>

              <form method="post" action="{{ route('register.pay') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Payment Method</label>
                  <select name="payment_method" class="form-select" required>
                    <option value="">Select</option>
                    <option value="card">Credit/Debit Card</option>
                    <option value="paypal">PayPal</option>
                  </select>
                </div>
                <div class="border rounded p-3 mb-3">
                  <div class="text-muted mb-2">Card Details</div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Cardholder Name</label>
                      <input name="card_name" type="text" class="form-control" placeholder="Name on card">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Card Number</label>
                      <input name="card_number" type="text" class="form-control" placeholder="•••• •••• •••• ••••">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Expiry (MM/YY)</label>
                      <input name="card_expiry" type="text" class="form-control" placeholder="MM/YY">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">CVC</label>
                      <input name="card_cvc" type="text" class="form-control" placeholder="CVC">
                    </div>
                  </div>
                  <div class="form-text">We do not store card numbers. Payment is processed securely.</div>
                </div>
                <div class="d-flex justify-content-between">
                  <a href="{{ route('register.start') }}" class="btn btn-outline-secondary">Back</a>
                  <button class="btn btn-primary">Pay and Create Account</button>
                </div>
              </form>
              <div class="mt-3">
                <form method="get" action="{{ route('register.resume', $reg->resume_token) }}">
                  <button class="btn btn-link">Save progress and resume later</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
