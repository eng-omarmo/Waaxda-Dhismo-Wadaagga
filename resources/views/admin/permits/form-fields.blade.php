@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="applicant_name" class="form-label">Applicant Name</label>
            <input type="text" class="form-control" id="applicant_name" name="applicant_name" value="{{ old('applicant_name', $permit->applicant_name ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="national_id_or_company_registration" class="form-label">National ID or Company Registration</label>
            <input type="text" class="form-control" id="national_id_or_company_registration" name="national_id_or_company_registration" value="{{ old('national_id_or_company_registration', $permit->national_id_or_company_registration ?? '') }}" required>
        </div>
    </div>
</div>

<hr>
<h5>Land Information</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="land_plot_number" class="form-label">Land Plot Number</label>
            <input type="text" class="form-control" id="land_plot_number" name="land_plot_number" value="{{ old('land_plot_number', $permit->land_plot_number ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $permit->location ?? '') }}" required>
        </div>
    </div>
</div>

<hr>
<h5>Apartment Details</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="number_of_floors" class="form-label">Number of Floors</label>
            <input type="number" class="form-control" id="number_of_floors" name="number_of_floors" value="{{ old('number_of_floors', $permit->number_of_floors ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="number_of_units" class="form-label">Number of Units</label>
            <input type="number" class="form-control" id="number_of_units" name="number_of_units" value="{{ old('number_of_units', $permit->number_of_units ?? '') }}" required>
        </div>
    </div>
</div>

<hr>
<h5>Technical Approval</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="engineer_or_architect_name" class="form-label">Engineer or Architect Name</label>
            <input type="text" class="form-control" id="engineer_or_architect_name" name="engineer_or_architect_name" value="{{ old('engineer_or_architect_name', $permit->engineer_or_architect_name ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="engineer_or_architect_license" class="form-label">Engineer or Architect License</label>
            <input type="text" class="form-control" id="engineer_or_architect_license" name="engineer_or_architect_license" value="{{ old('engineer_or_architect_license', $permit->engineer_or_architect_license ?? '') }}">
        </div>
    </div>
</div>
<div class="mb-3">
    <label for="approved_drawings" class="form-label">Approved Drawings (PDF, DWG, ZIP)</label>
    <input type="file" class="form-control" id="approved_drawings" name="approved_drawings">
    @if(isset($permit) && $permit->approved_drawings_path)
    <div class="mt-2">Current file: <a href="{{ route('admin.permits.download', $permit) }}">Download</a></div>
    @endif
</div>

<hr>
<h5>Permit Status</h5>
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="permit_status" class="form-label">Status</label>
            <select class="form-select" id="permit_status" name="permit_status" required>
                <option value="Pending" {{ old('permit_status', $permit->permit_status ?? '') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ old('permit_status', $permit->permit_status ?? '') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ old('permit_status', $permit->permit_status ?? '') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="permit_issue_date" class="form-label">Permit Issue Date</label>
            <input type="date" class="form-control" id="permit_issue_date" name="permit_issue_date" value="{{ old('permit_issue_date', isset($permit) && $permit->permit_issue_date ? $permit->permit_issue_date->format('Y-m-d') : '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="permit_expiry_date" class="form-label">Permit Expiry Date</label>
            <input type="date" class="form-control" id="permit_expiry_date" name="permit_expiry_date" value="{{ old('permit_expiry_date', isset($permit) && $permit->permit_expiry_date ? $permit->permit_expiry_date->format('Y-m-d') : '') }}">
        </div>
    </div>
</div>

<hr>
<h5>Approval Notes & Signature</h5>
<div class="row">
  <div class="col-md-6">
    <div class="mb-3">
      <label for="approval_notes" class="form-label">Approval Notes</label>
      <textarea id="approval_notes" name="approval_notes" class="form-control" rows="3">{{ old('approval_notes', $permit->approval_notes ?? '') }}</textarea>
    </div>
  </div>
  <div class="col-md-6">
    <div class="mb-3">
      <label class="form-label">Electronic Signature</label>
      <div class="border rounded p-2">
        <canvas id="permitSignaturePad" width="600" height="180" style="width:100%" aria-label="Signature pad"></canvas>
      </div>
      <input type="hidden" name="digital_signature_svg" id="permitSignatureData">
      <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="permitClearSignature">Clear</button>
      @if(isset($permit) && $permit->approval_signature_svg)
      <div class="mt-2">
        Current signature captured
      </div>
      @endif
    </div>
  </div>
</div>

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
