<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Project Details</title>
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
                  <div><strong>Step 3</strong> of 6</div>
                  <div class="text-muted">Project specific details</div>
                </div>
                <div class="progress mt-2"><div class="progress-bar" style="width: 50%"></div></div>
              </div>
              <form method="post" action="{{ route('portal.details.store') }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Project Name</label>
                  <input name="project_name" type="text" class="form-control" required value="{{ old('project_name', data_get($reg->data,'project_name')) }}">
                </div>
                <div class="mb-3">
                  <label class="form-label">Location</label>
                  <input name="location_text" type="text" class="form-control" required value="{{ old('location_text', data_get($reg->data,'location_text')) }}">
                </div>
                <div class="d-flex justify-content-between">
                  <a href="{{ route('portal.info') }}" class="btn btn-outline-secondary">Back</a>
                  <button class="btn btn-primary">Continue</button>
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
