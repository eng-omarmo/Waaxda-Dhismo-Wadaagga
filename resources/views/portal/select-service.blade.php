<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Select Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="mb-4">
                <div class="d-flex justify-content-between">
                  <div><strong>Step 1</strong> of 6</div>
                  <div class="text-muted">Choose a service</div>
                </div>
                <div class="progress mt-2">
                  <div class="progress-bar" style="width: 16%"></div>
                </div>
              </div>
              <form method="post" action="{{ route('portal.service.store') }}">
                @csrf
                <div class="list-group">
                  @foreach($services as $s)
                    <label class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <input class="form-check-input me-3" type="radio" name="serviceId" value="{{ $s->id }}" required>
                        <span class="fw-bold">{{ $s->name }}</span>
                        <div class="small text-muted">{{ $s->description }}</div>
                      </div>
                      <span class="badge bg-{{ $s->price > 0 ? 'primary' : 'secondary' }}">{{ $s->price > 0 ? ('$'.number_format($s->price,2)) : 'Free' }}</span>
                    </label>
                  @endforeach
                </div>
                <div class="d-flex justify-content-between mt-3">
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
