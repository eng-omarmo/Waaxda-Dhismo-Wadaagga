@extends('layouts.mazer')
@section('title','Generate Certificate')
@section('page-heading','Generate Certificate')
@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Select Service</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Service</label>
                    <select id="serviceSelect" class="form-select">
                        <option value="" selected disabled>Select a service</option>
                        @foreach($services as $s)
                        <option value="{{ $s->id }}" data-slug="{{ $s->slug }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-muted small">Choose a service to load its certification requirements.</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Certification Details</h6>
                <div class="small text-muted">Mandatory + service-specific fields</div>
            </div>
            <div class="card-body">
                <form id="certificateForm" method="POST" action="{{ route('admin.certificates.store') }}">
                    @csrf
                    <input type="hidden" name="template_slug" id="templateSlug">
                    <input type="hidden" name="fields" id="fieldsPayload">
                    <div class="mb-3">
                        <label class="form-label">Identifier Type</label>
                        <select class="form-select" name="identifier_type" id="identifierType">
                            <option value="">Project ID (default)</option>
                            <option value="project">Project ID</option>
                            <option value="license">Business License ID</option>
                            <option value="permit">Apartment Construction ID</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="form-text">Select identification type to standardize on certificate.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Identifier Value</label>
                        <input type="text" class="form-control" name="identifier_value" id="identifierValue" placeholder="e.g., UUID for project, BL-000123 for license">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Project ID</label>
                        <input type="text" class="form-control" name="project_id" id="projectId" placeholder="Paste project ID" required>
                        <div class="form-text">Project ID is required to bind certificate.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Certificate Title</label>
                        <input type="text" class="form-control" name="certificate_title" id="certificateTitle" placeholder="e.g., Certificate of Clearance" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Issue Date</label>
                        <input type="date" class="form-control" name="issued_at" id="issuedAt" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div id="serviceFields"></div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" id="previewBtn">Preview</button>
                        <button type="button" class="btn btn-outline-primary" id="downloadPdfBtn" disabled>Download PDF</button>
                        <button type="button" class="btn btn-outline-success" id="downloadPngBtn" disabled>Download PNG</button>
                        <button type="submit" class="btn btn-primary ms-auto" id="saveBtn" disabled>Save Certificate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Certificate Preview</h6>
                <span class="small text-muted">Branding is applied automatically</span>
            </div>
            <div class="card-body">
                <div id="certificatePreview" class="border rounded p-3 bg-white" style="min-height:300px"></div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
    const serviceSelect = document.getElementById('serviceSelect');
    const serviceFields = document.getElementById('serviceFields');
    const previewEl = document.getElementById('certificatePreview');
    const templateSlugInput = document.getElementById('templateSlug');
    const fieldsPayloadInput = document.getElementById('fieldsPayload');
    const previewBtn = document.getElementById('previewBtn');
    const downloadPdfBtn = document.getElementById('downloadPdfBtn');
    const downloadPngBtn = document.getElementById('downloadPngBtn');
    const saveBtn = document.getElementById('saveBtn');
    const certificateTitle = document.getElementById('certificateTitle');
    const issuedAt = document.getElementById('issuedAt');
    const projectId = document.getElementById('projectId');
    const identifierType = document.getElementById('identifierType');
    const identifierValue = document.getElementById('identifierValue');

    let currentTemplate = null;
    let currentFields = {};
    let stdId = '';

    serviceSelect.addEventListener('change', async () => {
        const serviceId = serviceSelect.value;
        resetUI();
        if (!serviceId) return;
        const res = await fetch(`{{ url('/admin/certificates/templates') }}/${serviceId}`);
        const tpl = await res.json();
        currentTemplate = tpl;
        templateSlugInput.value = tpl.template_slug || '';
        renderFields(tpl.variables_schema || []);
    });

    previewBtn.addEventListener('click', () => {
        if (!validateBase()) return;

        currentFields.standardized_id = computeStandardId();
        currentFields.uid ??= genUID();

        fieldsPayloadInput.value = JSON.stringify(currentFields);

        let rendered = currentTemplate?.html_template || defaultTemplate();

        Object.entries({
            title: certificateTitle.value,
            service: serviceSelect.options[serviceSelect.selectedIndex].text,
            project: projectId.value,
            date: issuedAt.value,
            standardized_id: currentFields.standardized_id,
            uid: currentFields.uid,
            ...currentFields
        }).forEach(([k, v]) => {
            rendered = rendered.replace(
                new RegExp(`\\@?\\{\\{${k}\\}\\}`, 'g'),
                escapeHtml(v ?? '')
            );
        });

        previewEl.innerHTML = rendered;

        downloadPdfBtn.disabled = false;
        downloadPngBtn.disabled = false;
        saveBtn.disabled = false;
    });


    downloadPdfBtn.addEventListener('click', async () => {
        const canvas = await html2canvas(previewEl, {
            scale: 2
        });
        const imgData = canvas.toDataURL('image/png');
        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF('p', 'pt', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        const ratio = Math.min(pageWidth / canvas.width, pageHeight / canvas.height);
        const imgWidth = canvas.width * ratio;
        const imgHeight = canvas.height * ratio;
        const x = (pageWidth - imgWidth) / 2;
        const y = 40;
        pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
        pdf.save('certificate.pdf');
    });

    downloadPngBtn.addEventListener('click', async () => {
        const canvas = await html2canvas(previewEl, {
            scale: 2
        });
        const link = document.createElement('a');
        link.download = 'certificate.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });

    function renderFields(schema) {
        serviceFields.innerHTML = '';
        schema.forEach(field => {
            const wrap = document.createElement('div');
            wrap.className = 'mb-3';
            const id = `field_${field.key}`;
            const label = document.createElement('label');
            label.className = 'form-label';
            label.textContent = field.label || field.key;
            label.setAttribute('for', id);
            let input;
            if (field.type === 'textarea') {
                input = document.createElement('textarea');
                input.className = 'form-control';
                input.rows = 4;
            } else if (field.type === 'select' && Array.isArray(field.options)) {
                input = document.createElement('select');
                input.className = 'form-select';
                field.options.forEach(opt => {
                    const o = document.createElement('option');
                    o.value = opt.value ?? opt;
                    o.textContent = opt.label ?? opt;
                    input.appendChild(o);
                });
            } else {
                input = document.createElement('input');
                input.type = field.type || 'text';
                input.className = 'form-control';
            }
            input.id = id;
            input.required = !!field.required;
            input.addEventListener('input', () => {
                currentFields[field.key] = input.value;
                fieldsPayloadInput.value = JSON.stringify(currentFields);
            });
            wrap.appendChild(label);
            wrap.appendChild(input);
            serviceFields.appendChild(wrap);
        });
    }

    function resetUI() {
        serviceFields.innerHTML = '';
        previewEl.innerHTML = '';
        templateSlugInput.value = '';
        fieldsPayloadInput.value = '';
        currentFields = {};
        currentTemplate = null;
        downloadPdfBtn.disabled = true;
        downloadPngBtn.disabled = true;
        saveBtn.disabled = true;
    }

    function validateBase() {
        if (!serviceSelect.value) return false;
        if (!projectId.value) return false;
        if (!certificateTitle.value) return false;
        if (!issuedAt.value) return false;
        const requiredMissing = Array.from(serviceFields.querySelectorAll('textarea,input,select')).some(el => el.required && !el.value);
        return !requiredMissing;
    }

    function defaultTemplate() {
        const logoUrl = "{{ asset('assets/images/logo/logo.png') }}";
        return '<div class="p-4"><div class="d-flex align-items-center mb-3"><img src="'+logoUrl+'" alt="Logo" style="height:40px" class="me-2"><h4 class="mb-0">@{{title}}</h4></div><div>Service: @{{service}}</div><div>Project: @{{project}}</div><div>Date: @{{date}}</div><div>UID: @{{uid}}</div><div>Standardized ID: @{{standardized_id}}</div><hr><div><strong>Details</strong></div><div>Parameters: @{{parameters}}</div><div>Configurations: @{{configurations}}</div><div>Compliance: @{{compliance}}</div></div>';
    }

    function genUID() {
        return 'UID-' + Math.random().toString(36).slice(2, 10).toUpperCase();
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        } [s]));
    }

    function computeStandardId() {
        const t = (identifierType.value || 'project').toLowerCase();
        let v = identifierValue.value || '';
        if (t === 'project') {
            v = projectId.value;
        }
        if (!v) return '';
        return `IPAMS:${t.toUpperCase()}:${String(v).trim().toUpperCase()}`;
    }
</script>
@endpush
@endsection
