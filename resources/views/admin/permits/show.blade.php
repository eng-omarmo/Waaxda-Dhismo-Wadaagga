@extends('layouts.mazer')

@section('title', 'Permit Details')
@section('page-heading', 'Permit for ' . $permit->applicant_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permit Details</h4>
                <a href="{{ route('admin.permits.index') }}" class="btn btn-secondary float-end">Back to List</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Applicant Information</h5>
                        <p><strong>Applicant:</strong> {{ $permit->applicant_name }}</p>
                        <p><strong>ID/Reg No.:</strong> {{ $permit->national_id_or_company_registration }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Land Information</h5>
                        <p><strong>Plot Number:</strong> {{ $permit->land_plot_number }}</p>
                        <p><strong>Location:</strong> {{ $permit->location }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Apartment Details</h5>
                        <p><strong>Floors:</strong> {{ $permit->number_of_floors }}</p>
                        <p><strong>Units:</strong> {{ $permit->number_of_units }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Technical Approval</h5>
                        <p><strong>Engineer/Architect:</strong> {{ $permit->engineer_or_architect_name }}</p>
                        <p><strong>License:</strong> {{ $permit->engineer_or_architect_license ?? 'N/A' }}</p>
                        <p><strong>Approved Drawings:</strong>
                            @if($permit->approved_drawings_path)
                            <a href="{{ route('admin.permits.download', $permit) }}" class="btn btn-sm btn-info">Download</a>
                            @else
                            Not Uploaded
                            @endif
                        </p>
                    </div>
                </div>
                <hr>
                <h5>Permit Status</h5>
                <p><strong>Status:</strong> <span class="badge bg-{{ $permit->permit_status == 'Approved' ? 'success' : ($permit->permit_status == 'Pending' ? 'warning' : 'danger') }}">{{ $permit->permit_status }}</span></p>
                <p><strong>Issue Date:</strong> {{ $permit->permit_issue_date ? $permit->permit_issue_date->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Expiry Date:</strong> {{ $permit->permit_expiry_date ? $permit->permit_expiry_date->format('Y-m-d') : 'N/A' }}</p>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.permits.approve', $permit) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Approval Notes</label>
                                <textarea name="approval_notes" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Electronic Signature</label>
                                <div class="border rounded p-2">
                                    <canvas id="permitSignaturePad" width="600" height="180" style="width:100%" aria-label="Signature pad"></canvas>
                                </div>
                                <input type="hidden" name="digital_signature_svg" id="permitSignatureData" required>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="permitClearSignature">Clear</button>
                            </div>
                            <button type="submit" class="btn btn-success">Approve Permit</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.permits.reject', $permit) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger">Reject Permit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var canvas = document.getElementById('permitSignaturePad');
  var clearBtn = document.getElementById('permitClearSignature');
  var hidden = document.getElementById('permitSignatureData');
  if (!canvas) return;
  var ctx = canvas.getContext('2d');
  var drawing = false;
  var lastPos = { x: 0, y: 0 };
  function getPos(e) {
    var rect = canvas.getBoundingClientRect();
    var x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
    var y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
    return { x: x, y: y };
  }
  function updateHidden() {
    var dataUrl = canvas.toDataURL('image/png');
    hidden.value = dataUrl;
  }
  canvas.addEventListener('mousedown', function(e) { drawing = true; lastPos = getPos(e); });
  canvas.addEventListener('mousemove', function(e) {
    if (!drawing) return;
    var pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(lastPos.x, lastPos.y);
    ctx.lineTo(pos.x, pos.y);
    ctx.strokeStyle = '#111';
    ctx.lineWidth = 2;
    ctx.stroke();
    lastPos = pos;
    updateHidden();
  });
  canvas.addEventListener('mouseup', function() { drawing = false; updateHidden(); });
  canvas.addEventListener('mouseleave', function() { drawing = false; });
  canvas.addEventListener('touchstart', function(e) { drawing = true; lastPos = getPos(e); }, {passive:true});
  canvas.addEventListener('touchmove', function(e) {
    if (!drawing) return;
    var pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(lastPos.x, lastPos.y);
    ctx.lineTo(pos.x, pos.y);
    ctx.strokeStyle = '#111';
    ctx.lineWidth = 2;
    ctx.stroke();
    lastPos = pos;
    updateHidden();
  }, {passive:true});
  canvas.addEventListener('touchend', function() { drawing = false; updateHidden(); });
  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      ctx.clearRect(0,0,canvas.width,canvas.height);
      hidden.value = '';
    });
  }
});
</script>
@endpush
