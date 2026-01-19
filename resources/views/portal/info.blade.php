<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Service Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  </head>
  <body class="bg-light">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="mb-4">Complete Your Application</h5>
              @if ($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
              <form method="post" action="{{ route('portal.info.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-bold">Your Details</div>
                    <span class="badge bg-light text-primary">{{ $reg->service_slug }}</span>
                  </div>
                </div>
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
                @if(in_array($reg->service_slug, ['project-registration','construction-permit-application']))
                <hr>
                <div class="mb-3 fw-bold">Project Details</div>
                <div class="mb-3">
                  <label class="form-label">Project Name</label>
                  <input name="project_name" type="text" class="form-control" value="{{ old('project_name', data_get($reg->data,'project_name')) }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Location</label>
                  <input name="location_text" type="text" class="form-control" value="{{ old('location_text', data_get($reg->data,'location_text')) }}">
                </div>
                @endif
                <hr>
                <div class="mb-3 fw-bold">Supporting Documents</div>
                <div class="mb-3">
                  <label class="form-label">Upload Documents</label>
                  <input name="documents[]" type="file" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                  <div class="form-text">Allowed: PDF, JPG, PNG, DOC, DOCX. Max 5MB per file.</div>
                </div>
                <hr>
                <div class="mb-3 fw-bold">Payment</div>
                @isset($service)
                  <div class="alert alert-info">Amount due: ${{ number_format($service->price,2) }}</div>
                @endisset
                <div class="d-flex justify-content-between">
                  <a href="{{ route('landing.page.index') }}" class="btn btn-outline-secondary">Back to Services</a>
                  <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary">Save Draft</button>
                    <button type="submit" name="payment_method" value="initialize" class="btn btn-primary">Initialize & Continue</button>
                  </div>
                </div>
              </form>
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
