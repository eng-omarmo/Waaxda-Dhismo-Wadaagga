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
                    <a href="{{ route('admin.certificates.download', $certificate) }}" class="btn btn-primary" id="download-btn">Download PDF</a>
                    <a href="{{ route('admin.certificates.download', ['certificate' => $certificate->id, 'sample' => 1]) }}" class="btn btn-outline-secondary" id="sample-download-btn">Download Sample PDF</a>
                    <a href="{{ $publicUrl }}" target="_blank" class="btn btn-outline-primary">Open Public Link</a>
                </div>
            </div>
            <div class="card-body">
                <div id="download-loading" class="d-none position-fixed top-0 start-0 w-100 h-100" style="background:rgba(255,255,255,0.7);z-index:1050">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status" aria-label="Generating PDF"></div>
                            <div class="mt-2 text-primary">Generating PDF...</div>
                        </div>
                    </div>
                </div>
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
                        <h6>Service</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name</strong></td>
                                <td>{{ $service?->name ?? 'Service' }}</td>
                            </tr>
                        </table>
                        @php
                            $sig = hash_hmac('sha256', (string) $certificate->certificate_uid, config('app.key'));
                            $verifyUrl = route('certificates.verify', ['uid' => $certificate->certificate_uid, 'sig' => $sig]);
                            $payload = json_encode([
                                'uid' => $certificate->certificate_uid,
                                'url' => $verifyUrl,
                                'issuer' => $service?->name ?? 'BRA',
                                'issued_at' => $certificate->issued_at->toDateString(),
                            ], JSON_UNESCAPED_SLASHES);
                        @endphp
                        <div class="mt-3">
                            <h6>QR Verification</h6>
                            <div id="qrCode" style="width:300px;height:300px;"></div>
                            <div class="small text-muted mt-2">Scan to verify</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('download-btn');
        const sampleBtn = document.getElementById('sample-download-btn');
        const overlay = document.getElementById('download-loading');
        const attach = function(el) {
            if (el && overlay) {
                el.addEventListener('click', function() {
                    overlay.classList.remove('d-none');
                    setTimeout(() => overlay.classList.add('d-none'), 8000);
                });
            }
        };
        attach(btn);
        attach(sampleBtn);
        var el = document.getElementById('qrCode');
        if (el) {
            var s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
            s.onload = function() {
                new QRCode(el, { text: {!! json_encode($payload) !!}, width: 300, height: 300, correctLevel: QRCode.CorrectLevel.M });
            };
            document.body.appendChild(s);
        }
    });
</script>
@endpush
@endsection
