<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Begin Registration</title>
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
                  <div><strong>Step 1</strong> of 2</div>
                  <div class="text-muted">Your details</div>
                </div>
                <div class="progress mt-2" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar" style="width: 50%"></div>
                </div>
              </div>
              <form method="post" action="{{ route('register.step1.store') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Full Name</label>
                  <input name="full_name" type="text" class="form-control" required placeholder="e.g., Mohamed Ali" value="{{ old('full_name') }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input name="email" type="email" class="form-control" required placeholder="you@example.com" value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Phone (optional)</label>
                  <input name="phone" type="text" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="d-flex justify-content-between">
                  <a href="{{ url('/') }}" class="btn btn-outline-secondary">Back</a>
                  <button class="btn btn-primary">Continue</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
