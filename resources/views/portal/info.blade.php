<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Your Information</title>
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
                  @php
                    $isPermit = ($reg->service_slug === 'construction-permit-application');
                    $stepLabel = $isPermit ? 'Step 1' : 'Step 2';
                    $ofLabel = $isPermit ? 'of 3' : 'of 6';
                    $progressPct = $isPermit ? 33 : 33;
                  @endphp
                  <div><strong>{{ $stepLabel }}</strong> {{ $ofLabel }}</div>
                  <div class="text-muted">Your details</div>
                </div>
                <div class="progress mt-2"><div class="progress-bar" style="width: {{ $progressPct }}%"></div></div>
              </div>
              <form method="post" action="{{ route('portal.info.store') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Full Name</label>
                  <input name="full_name" type="text" class="form-control" required value="{{ old('full_name', $reg->full_name) }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input name="email" type="email" class="form-control" required value="{{ old('email', $reg->email) }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Phone</label>
                  <input name="phone" type="text" class="form-control" value="{{ old('phone', $reg->phone) }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">National ID (optional)</label>
                  <input name="national_id" type="text" class="form-control" value="{{ old('national_id', data_get($reg->data,'national_id')) }}">
                </div>
                <div class="d-flex justify-content-between">
                  <a href="{{ route('portal.start') }}" class="btn btn-outline-secondary">Back</a>
                  <button class="btn btn-primary">Continue</button>
                </div>
              </form>
              @if ($errors->any())
              <div class="alert alert-danger mt-3">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
              <div class="mt-3">
                <a href="{{ route('portal.resume', $reg->resume_token) }}" class="btn btn-link">Save progress and resume later</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
