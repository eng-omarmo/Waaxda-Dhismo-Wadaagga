<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Supporting Documents</title>
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
                  <div><strong>Step 4</strong> of 6</div>
                  <div class="text-muted">Upload supporting documents</div>
                </div>
                <div class="progress mt-2"><div class="progress-bar" style="width: 66%"></div></div>
              </div>
              <form method="post" action="{{ route('portal.docs.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Upload Documents</label>
                  <input name="documents[]" type="file" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                  <div class="form-text">Allowed: PDF, JPG, PNG, DOC, DOCX. Max 5MB per file.</div>
                </div>
                <div class="d-flex justify-content-between">
                  <a href="{{ route('portal.details') }}" class="btn btn-outline-secondary">Back</a>
                  <button class="btn btn-primary">Continue</button>
                </div>
              </form>
              <hr>
              <h6>Uploaded</h6>
              <table class="table">
                <thead><tr><th>File</th><th>Uploaded</th></tr></thead>
                <tbody>
                  @forelse($docs as $d)
                    <tr><td>{{ $d->file_name }}</td><td>{{ $d->created_at?->toDateTimeString() }}</td></tr>
                  @empty
                    <tr><td colspan="2" class="text-muted">No documents uploaded.</td></tr>
                  @endforelse
                </tbody>
              </table>
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
