<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Service Status – IPAMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  <style>
    .hero { background: linear-gradient(135deg, #001a4d 0%, #002d80 100%); color: #fff; padding: 48px 0; }
  </style>
</head>
<body>
  <x-public-navbar />
  <header class="hero">
    <div class="container">
      <h1 class="fw-extrabold mb-2">{{ __('Track Your Service') }}</h1>
      <p class="mb-0">{{ __('Enter your reference ID to see the latest status.') }}</p>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <form method="POST" action="{{ route('track.lookup') }}" novalidate aria-describedby="helpText">
                @csrf
                <div class="row g-3 align-items-end">
                  <div class="col-md-7">
                    <label for="reference" class="form-label">{{ __('Service Reference ID (UUID)') }}</label>
                    <input id="reference" name="reference" type="text" class="form-control" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required pattern="^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$" value="{{ old('reference') }}" aria-required="true" aria-invalid="{{ $errors->has('reference') ? 'true' : 'false' }}">
                  </div>
                  <div class="col-md-5">
                    <label for="email" class="form-label">{{ __('Registrant Email (optional for full details)') }}</label>
                    <input id="email" name="email" type="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}">
                  </div>
                  <div class="col-12 d-flex gap-2">
                    <button id="searchBtn" type="submit" class="btn btn-primary">
                      <span class="spinner-border spinner-border-sm me-2 d-none" id="loader" role="status" aria-hidden="true"></span>
                      {{ __('Search') }}
                    </button>
                    <button type="reset" class="btn btn-outline-secondary" onclick="document.getElementById('results').innerHTML='';">{{ __('Clear') }}</button>
                  </div>
                </div>
                <div id="helpText" class="form-text mt-2">{{ __('Provide your email to view full details securely.') }}</div>
                @if ($errors->any())
                  <div class="alert alert-danger mt-3" role="alert">
                    <ul class="mb-0">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
              </form>

              <div id="results" class="mt-4" aria-live="polite">
                @if (!empty($data))
                  <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <h5 class="mb-0">{{ __('Current Status') }}</h5>
                      <span class="badge bg-primary">{{ $data['status'] }}</span>
                    </div>
                    <p class="mb-1"><strong>{{ __('Reference') }}:</strong> {{ $data['reference'] }}</p>
                    <p class="mb-1"><strong>{{ __('Last Updated') }}:</strong> {{ \Illuminate\Support\Carbon::parse($data['updated_at'])->toDateTimeString() }}</p>
                    <p class="mb-3"><strong>{{ __('Next Milestone') }}:</strong> {{ $data['next_milestone'] }}</p>
                    @if ($data['email_provided'] && $data['email_matched'])
                      <div class="alert alert-success" role="alert">
                        {{ __('Email matched. Showing full details.') }}
                      </div>
                      <p class="mb-1"><strong>{{ __('Contact for inquiries') }}:</strong> {{ $data['contact']['email'] }} · {{ $data['contact']['phone'] }}</p>
                    @elseif($data['email_provided'] && !$data['email_matched'])
                      <div class="alert alert-warning" role="alert">
                        {{ __('The provided email does not match our records. Showing limited status.') }}
                      </div>
                    @else
                      <div class="alert alert-info" role="alert">
                        {{ __('Provide your registrant email to view full details securely.') }}
                      </div>
                    @endif
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-3">{{ __('Guidelines') }}</h6>
              <ul class="small">
                <li>{{ __('Use your UUID reference from the submission receipt.') }}</li>
                <li>{{ __('Enter registrant email to view full details.') }}</li>
                <li>{{ __('If no record is found, check the reference and try again.') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="py-4 bg-white border-top">
    <div class="container text-center">
      <p class="mb-0 text-muted fw-bold">© {{ date('Y') }} Dowladda Hoose ee Xamar – Integrated Property & Apartment Management System</p>
    </div>
  </footer>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    const btn = document.getElementById('searchBtn');
    const loader = document.getElementById('loader');
    if (btn && loader) {
      btn.addEventListener('click', () => {
        loader.classList.remove('d-none');
        btn.setAttribute('aria-busy', 'true');
      });
    }
  </script>
</body>
</html>

