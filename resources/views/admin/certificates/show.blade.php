@extends('layouts.mazer')
@section('title','Certificate of Clearance')
@section('page-heading','Certificate of Clearance')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Certificate {{ $certificate->certificate_number }}</h5>
        @php
          $publicUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('certificate.public', now()->addDays(14), ['certificate' => $certificate->id]);
        @endphp
        <div class="d-flex gap-2">
          <a href="{{ route('admin.certificates.download', $certificate) }}" class="btn btn-primary">Download PDF</a>
          <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline-primary">Open Public Link</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Certificate Details</h6>
            <table class="table table-borderless">
              <tr>
                <td><strong>Number</strong></td>
                <td>{{ $certificate->certificate_number }}</td>
              </tr>
              <tr>
                <td><strong>UID</strong></td>
                <td>{{ $certificate->certificate_uid }}</td>
              </tr>
              <tr>
                <td><strong>Standardized ID</strong></td>
                <td>{{ $certificate->metadata['standardized_id'] ?? 'N/A' }}</td>
              </tr>
              <tr>
                <td><strong>Issued At</strong></td>
                <td>{{ $certificate->issued_at->format('Y-m-d H:i') }}</td>
              </tr>
              <tr>
                <td><strong>Hash</strong></td>
                <td class="text-break">{{ $certificate->certificate_hash }}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6>Project</h6>
            <table class="table table-borderless">
              <tr>
                <td><strong>Name</strong></td>
                <td>{{ $project->project_name }}</td>
              </tr>
              <tr>
                <td><strong>ID</strong></td>
                <td class="text-muted">{{ $project->id }}</td>
              </tr>
              <tr>
                <td><strong>Service</strong></td>
                <td>{{ $service?->name ?? 'Project Registration' }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
