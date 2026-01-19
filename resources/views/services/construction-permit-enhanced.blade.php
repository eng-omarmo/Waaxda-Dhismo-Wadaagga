<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Construction Permit Application – IPAMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  <style>
    :root {
      --mu-blue: #002d80;
      --mu-blue-dark: #001a4d;
      --mu-green: #1e7e34;
      --mu-dark: #121416;
      --mu-grey-bg: #f8f9fc;
    }
    body { 
      font-family: 'Nunito', sans-serif; 
      color: var(--mu-dark); 
      background-color: var(--mu-grey-bg);
    }
    .hero { 
      background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%); 
      color: #fff; 
      padding: 48px 0;
    }
    .section-heading { 
      font-weight: 800; 
      color: var(--mu-blue-dark); 
      border-bottom: 2px solid #e9ecef;
      padding-bottom: 12px;
      margin-bottom: 24px;
    }
    .form-section {
      background: white;
      border-radius: 8px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .form-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 8px;
    }
    .form-label .required {
      color: #dc3545;
    }
    .form-label .help-icon {
      color: var(--mu-blue);
      cursor: help;
      margin-left: 4px;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--mu-blue);
      box-shadow: 0 0 0 0.2rem rgba(0, 45, 128, 0.25);
    }
    .form-text-help {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 4px;
    }
    .form-error {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 4px;
      display: none;
    }
    .form-error.show {
      display: block;
    }
    .field-group {
      margin-bottom: 20px;
    }
    .progress-indicator {
      background: #e9ecef;
      border-radius: 10px;
      height: 8px;
      margin-bottom: 24px;
      overflow: hidden;
    }
    .progress-indicator-bar {
      background: linear-gradient(90deg, var(--mu-blue) 0%, var(--mu-green) 100%);
      height: 100%;
      transition: width 0.3s ease;
      border-radius: 10px;
    }
    .progress-text {
      text-align: center;
      color: #6c757d;
      font-size: 0.875rem;
      margin-bottom: 8px;
    }
    .save-indicator {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: var(--mu-green);
      color: white;
      padding: 12px 20px;
      border-radius: 6px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      display: none;
      z-index: 1000;
      animation: slideIn 0.3s ease;
    }
    .save-indicator.show {
      display: block;
    }
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    .review-section {
      display: none;
    }
    .review-section.active {
      display: block;
    }
    .review-item {
      padding: 12px;
      border-bottom: 1px solid #e9ecef;
    }
    .review-item:last-child {
      border-bottom: none;
    }
    .review-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 4px;
    }
    .review-value {
      color: #6c757d;
    }
    .btn-group-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 24px;
    }
    .btn {
      font-weight: 600;
      padding: 12px 24px;
    }
    .file-upload-area {
      border: 2px dashed #dee2e6;
      border-radius: 8px;
      padding: 24px;
      text-align: center;
      transition: border-color 0.3s;
    }
    .file-upload-area:hover {
      border-color: var(--mu-blue);
    }
    .file-list {
      margin-top: 12px;
    }
    .file-item {
      padding: 8px;
      background: #f8f9fa;
      border-radius: 4px;
      margin-bottom: 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    @media (max-width: 768px) {
      .hero {
        padding: 32px 0;
      }
      .form-section {
        padding: 16px;
      }
      .btn-group-actions {
        flex-direction: column;
      }
      .btn-group-actions .btn {
        width: 100%;
      }
    }
    .form-control:invalid, .form-select:invalid {
      border-color: #dc3545;
    }
    .form-control:valid:not(:placeholder-shown), .form-select:valid {
      border-color: #28a745;
    }
    [data-bs-toggle="tooltip"] {
      cursor: help;
    }
    .btn:focus, .form-control:focus, .form-select:focus {
      outline: 2px solid var(--mu-blue);
      outline-offset: 2px;
    }
  </style>
  @stack('page-styles')
</head>
<body>
  <x-public-navbar />

  <header class="hero">
    <div class="container">
      <h1 class="fw-extrabold mb-2">Construction Permit Application</h1>
      <p class="mb-0">Apply for a construction permit to build on your land. All fields marked with <span class="required">*</span> are required.</p>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <!-- Progress Indicator -->
          <div class="progress-text">
            <span id="progressText">0% Complete</span>
          </div>
          <div class="progress-indicator">
            <div class="progress-indicator-bar" id="progressBar" style="width: 0%"></div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              <h6 class="alert-heading fw-bold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>Please correct the following errors:
              </h6>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Form Section -->
          <div id="formSection" class="form-section">
            <form id="constructionPermitForm" method="POST" action="{{ route('services.construction-permit.store') }}" enctype="multipart/form-data" novalidate>
              @csrf
              @if(isset($payment))
              <input type="hidden" name="payment_id" value="{{ $payment->id }}">
              @endif

              <!-- Section 1: Applicant Information -->
              <h5 class="section-heading">
                <i class="bi bi-person-badge me-2"></i>Applicant Information
              </h5>

              <div class="row g-3">
                <div class="col-md-12 field-group">
                  <label for="applicant_full_name" class="form-label">
                    Full Name <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Enter your full legal name as it appears on official documents"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="applicant_full_name" 
                    name="applicant_full_name" 
                    placeholder="Enter full name"
                    value="{{ old('applicant_full_name', '') }}"
                    required
                    aria-describedby="applicant_full_name_help applicant_full_name_error"
                    autocomplete="name">
                  <div class="form-text-help" id="applicant_full_name_help">
                    Use your legal name for official records
                  </div>
                  <div class="form-error" id="applicant_full_name_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="applicant_national_id" class="form-label">
                    National ID / Passport <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Your national identification number or passport number"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="applicant_national_id" 
                    name="applicant_national_id" 
                    placeholder="ID Number"
                    value="{{ old('applicant_national_id', '') }}"
                    required
                    aria-describedby="applicant_national_id_help applicant_national_id_error">
                  <div class="form-text-help" id="applicant_national_id_help">
                    National ID or passport number
                  </div>
                  <div class="form-error" id="applicant_national_id_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="applicant_role" class="form-label">
                    Your Role <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Select your relationship to this construction project"></i>
                  </label>
                  <select 
                    class="form-select" 
                    id="applicant_role" 
                    name="applicant_role" 
                    required
                    aria-describedby="applicant_role_help applicant_role_error">
                    <option value="">Select role...</option>
                    <option value="Owner" {{ old('applicant_role') == 'Owner' ? 'selected' : '' }}>Owner</option>
                    <option value="Legal Representative" {{ old('applicant_role') == 'Legal Representative' ? 'selected' : '' }}>Legal Representative</option>
                    <option value="Developer" {{ old('applicant_role') == 'Developer' ? 'selected' : '' }}>Developer</option>
                  </select>
                  <div class="form-text-help" id="applicant_role_help">
                    Your relationship to this construction project
                  </div>
                  <div class="form-error" id="applicant_role_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="applicant_phone" class="form-label">
                    Phone Number <span class="required">*</span>
                  </label>
                  <input 
                    type="tel" 
                    class="form-control" 
                    id="applicant_phone" 
                    name="applicant_phone" 
                    placeholder="0612345678"
                    value="{{ old('applicant_phone', '') }}"
                    required
                    pattern="[0-9]{9,12}"
                    aria-describedby="applicant_phone_help applicant_phone_error"
                    autocomplete="tel">
                  <div class="form-text-help" id="applicant_phone_help">
                    Format: 9-12 digits without spaces or dashes
                  </div>
                  <div class="form-error" id="applicant_phone_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="applicant_email" class="form-label">
                    Email Address
                  </label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="applicant_email" 
                    name="applicant_email" 
                    placeholder="name@example.com"
                    value="{{ old('applicant_email', '') }}"
                    aria-describedby="applicant_email_help applicant_email_error"
                    autocomplete="email">
                  <div class="form-text-help" id="applicant_email_help">
                    Optional: for notifications and updates
                  </div>
                  <div class="form-error" id="applicant_email_error" role="alert"></div>
                </div>

                <div class="col-md-12 field-group">
                  <label for="applicant_address" class="form-label">
                    Address <span class="required">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="applicant_address" 
                    name="applicant_address" 
                    placeholder="District, Neighborhood, House No."
                    value="{{ old('applicant_address', '') }}"
                    required
                    aria-describedby="applicant_address_help applicant_address_error"
                    autocomplete="street-address">
                  <div class="form-text-help" id="applicant_address_help">
                    Your current residential or business address
                  </div>
                  <div class="form-error" id="applicant_address_error" role="alert"></div>
                </div>
              </div>

              <!-- Section 2: Land Details -->
              <h5 class="section-heading mt-4">
                <i class="bi bi-map me-2"></i>Land Information
              </h5>

              <div class="row g-3">
                <div class="col-md-6 field-group">
                  <label for="plot_number" class="form-label">
                    Plot Number <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Official plot number from land registry"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="plot_number" 
                    name="plot_number" 
                    placeholder="e.g. PLT-990"
                    value="{{ old('plot_number', '') }}"
                    required
                    aria-describedby="plot_number_help plot_number_error">
                  <div class="form-text-help" id="plot_number_help">
                    Official plot number from land registry
                  </div>
                  <div class="form-error" id="plot_number_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="land_title_number" class="form-label">
                    Title Deed Number <span class="required">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="land_title_number" 
                    name="land_title_number" 
                    placeholder="e.g. T-12345"
                    value="{{ old('land_title_number', '') }}"
                    required
                    aria-describedby="land_title_number_help land_title_number_error">
                  <div class="form-text-help" id="land_title_number_help">
                    Land title deed number from registry
                  </div>
                  <div class="form-error" id="land_title_number_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="land_size_sqm" class="form-label">
                    Land Size (square meters) <span class="required">*</span>
                  </label>
                  <div class="input-group">
                    <input 
                      type="number" 
                      class="form-control" 
                      id="land_size_sqm" 
                      name="land_size_sqm" 
                      placeholder="e.g. 400"
                      value="{{ old('land_size_sqm', '') }}"
                      required
                      min="1"
                      aria-describedby="land_size_sqm_help land_size_sqm_error">
                    <span class="input-group-text">m²</span>
                  </div>
                  <div class="form-text-help" id="land_size_sqm_help">
                    Total land area in square meters
                  </div>
                  <div class="form-error" id="land_size_sqm_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="land_location_district" class="form-label">
                    District <span class="required">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="land_location_district" 
                    name="land_location_district" 
                    placeholder="e.g. Hodan"
                    value="{{ old('land_location_district', '') }}"
                    required
                    aria-describedby="land_location_district_help land_location_district_error">
                  <div class="form-text-help" id="land_location_district_help">
                    District where the land is located
                  </div>
                  <div class="form-error" id="land_location_district_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="land_use_zoning" class="form-label">
                    Zoning Type <span class="required">*</span>
                  </label>
                  <select 
                    class="form-select" 
                    id="land_use_zoning" 
                    name="land_use_zoning" 
                    required
                    aria-describedby="land_use_zoning_help land_use_zoning_error">
                    <option value="">Select zoning...</option>
                    <option value="Residential" {{ old('land_use_zoning') == 'Residential' ? 'selected' : '' }}>Residential</option>
                    <option value="Commercial" {{ old('land_use_zoning') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="Mixed" {{ old('land_use_zoning') == 'Mixed' ? 'selected' : '' }}>Mixed Use</option>
                  </select>
                  <div class="form-text-help" id="land_use_zoning_help">
                    Official zoning designation for the land
                  </div>
                  <div class="form-error" id="land_use_zoning_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="land_ownership_type" class="form-label">
                    Ownership Type <span class="required">*</span>
                  </label>
                  <select 
                    class="form-select" 
                    id="land_ownership_type" 
                    name="land_ownership_type" 
                    required
                    aria-describedby="land_ownership_type_help land_ownership_type_error">
                    <option value="">Select ownership type...</option>
                    <option value="Private" {{ old('land_ownership_type') == 'Private' ? 'selected' : '' }}>Private</option>
                    <option value="Shared" {{ old('land_ownership_type') == 'Shared' ? 'selected' : '' }}>Shared</option>
                    <option value="Government" {{ old('land_ownership_type') == 'Government' ? 'selected' : '' }}>Government</option>
                  </select>
                  <div class="form-text-help" id="land_ownership_type_help">
                    Type of land ownership
                  </div>
                  <div class="form-error" id="land_ownership_type_error" role="alert"></div>
                </div>
              </div>

              <!-- Section 3: Documents -->
              <h5 class="section-heading mt-4">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>Supporting Documents
              </h5>

              <div class="field-group">
                <label for="documents" class="form-label">
                  Upload Documents
                  <i class="bi bi-info-circle help-icon" 
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top"
                     title="Upload ID, title deed, and other supporting documents"></i>
                </label>
                <input 
                  type="file" 
                  class="form-control" 
                  id="documents" 
                  name="documents[]" 
                  multiple 
                  accept=".pdf,.jpg,.jpeg,.png"
                  aria-describedby="documents_help documents_error">
                <div class="form-text-help" id="documents_help">
                  You can upload multiple files (PDF, JPG, PNG). Max 5MB per file.
                </div>
                <div id="fileList" class="file-list"></div>
                <div class="form-error" id="documents_error" role="alert"></div>
              </div>

              <div class="form-check field-group">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  id="termsCheck" 
                  name="terms"
                  required>
                <label class="form-check-label" for="termsCheck">
                  I confirm that all information provided is accurate and truthful <span class="required">*</span>
                </label>
                <div class="form-error" id="termsCheck_error" role="alert"></div>
              </div>

              <!-- Action Buttons -->
              <div class="btn-group-actions">
                <button type="button" class="btn btn-outline-primary" id="reviewBtn">
                  <i class="bi bi-eye me-2"></i>Review Before Submit
                </button>
                <button type="submit" class="btn btn-success" id="submitBtn">
                  <i class="bi bi-send me-2"></i>Submit Application
                </button>
              </div>
            </form>
          </div>

          <!-- Review Section -->
          <div id="reviewSection" class="review-section form-section">
            <h5 class="section-heading">
              <i class="bi bi-check-circle me-2"></i>Review Your Application
            </h5>
            <p class="text-muted mb-4">Please review all information before submitting. Click "Edit" to make changes.</p>
            
            <div id="reviewContent"></div>

            <div class="btn-group-actions">
              <button type="button" class="btn btn-outline-secondary" id="editBtn">
                <i class="bi bi-pencil me-2"></i>Edit Application
              </button>
              <button type="button" class="btn btn-success" id="confirmSubmitBtn">
                <i class="bi bi-check-circle me-2"></i>Confirm & Submit
              </button>
            </div>
          </div>

          <!-- Help Card -->
          <div class="card mt-4">
            <div class="card-body">
              <h6 class="card-title">
                <i class="bi bi-question-circle me-2"></i>Important Notes
              </h6>
              <ul class="small text-muted ps-3 mb-0">
                <li class="mb-2">Ensure your name matches your national ID or passport exactly.</li>
                <li class="mb-2">Plot number and Title Deed must match official government records.</li>
                <li>Uploaded documents must be clear and readable.</li>
              </ul>
              <hr>
              <p class="small mb-0">
                <strong>Need help?</strong> Contact us at <a href="mailto:support@ipams.so">support@ipams.so</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Auto-save Indicator -->
  <div class="save-indicator" id="saveIndicator" role="status" aria-live="polite">
    <i class="bi bi-check-circle me-2"></i><span>Draft saved automatically</span>
  </div>

  <footer class="py-4 bg-white border-top mt-5">
    <div class="container text-center">
      <p class="mb-0 text-muted fw-bold">© {{ date('Y') }} Dowladda Hoose ee Xamar – Integrated Property & Apartment Management System</p>
    </div>
  </footer>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    // Configuration
    const STORAGE_KEY = 'construction_permit_draft';
    const form = document.getElementById('constructionPermitForm');
    const formFields = [
      'applicant_full_name', 'applicant_national_id', 'applicant_role',
      'applicant_phone', 'applicant_email', 'applicant_address',
      'plot_number', 'land_title_number', 'land_size_sqm',
      'land_location_district', 'land_use_zoning', 'land_ownership_type'
    ];
    const termsCheckbox = document.getElementById('termsCheck');
    const submitBtn = document.getElementById('submitBtn');

    // Load saved draft
    function loadDraft() {
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) {
        try {
          const data = JSON.parse(saved);
          formFields.forEach(field => {
            const input = document.getElementById(field);
            if (input && data[field]) {
              input.value = data[field];
            }
          });
          updateProgress();
        } catch (e) {
          console.error('Error loading draft:', e);
        }
      }
    }

    // Save draft
    function saveDraft() {
      const data = {};
      formFields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
          data[field] = input.value;
        }
      });
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
      showSaveIndicator();
      updateProgress();
    }

    // Show save indicator
    function showSaveIndicator() {
      const indicator = document.getElementById('saveIndicator');
      indicator.classList.add('show');
      setTimeout(() => {
        indicator.classList.remove('show');
      }, 2000);
    }

    // Auto-save on input (debounced)
    let saveTimeout;
    formFields.forEach(field => {
      const input = document.getElementById(field);
      if (input) {
        input.addEventListener('input', () => {
          clearTimeout(saveTimeout);
          saveTimeout = setTimeout(saveDraft, 1000);
          validateField(input);
          updateProgress();
        });
        input.addEventListener('blur', () => {
          validateField(input);
        });
      }
    });

    // Real-time validation
    function validateField(input) {
      const errorDiv = document.getElementById(input.id + '_error');
      const isValid = input.checkValidity();
      
      if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (errorDiv) {
          errorDiv.classList.remove('show');
          errorDiv.textContent = '';
        }
      } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        if (errorDiv) {
          errorDiv.classList.add('show');
          errorDiv.textContent = input.validationMessage || 'This field is required';
        }
      }
      return isValid;
    }

    // Validate form
    function validateForm() {
      let isValid = true;
      formFields.forEach(field => {
        const input = document.getElementById(field);
        if (input && !validateField(input)) {
          isValid = false;
        }
      });
      
      // Check terms checkbox
      if (termsCheckbox && !termsCheckbox.checked) {
        isValid = false;
        const errorDiv = document.getElementById('termsCheck_error');
        if (errorDiv) {
          errorDiv.classList.add('show');
          errorDiv.textContent = 'You must confirm that all information is accurate';
        }
      }
      
      return isValid;
    }

    // Update progress
    function updateProgress() {
      let filled = 0;
      formFields.forEach(field => {
        const input = document.getElementById(field);
        if (input && input.value.trim()) {
          filled++;
        }
      });
      const percentage = (filled / formFields.length) * 100;
      document.getElementById('progressBar').style.width = percentage + '%';
      document.getElementById('progressText').textContent = Math.round(percentage) + '% Complete';
    }

    // File upload display
    const fileInput = document.getElementById('documents');
    const fileList = document.getElementById('fileList');
    if (fileInput) {
      fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';
        Array.from(this.files).forEach(file => {
          const fileItem = document.createElement('div');
          fileItem.className = 'file-item';
          fileItem.innerHTML = `
            <span><i class="bi bi-file-earmark me-2"></i>${file.name}</span>
            <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
          `;
          fileList.appendChild(fileItem);
        });
      });
    }

    // Review functionality
    document.getElementById('reviewBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        const reviewContent = document.getElementById('reviewContent');
        reviewContent.innerHTML = '';
        
        const sections = [
          { title: 'Applicant Information', fields: [
            'applicant_full_name', 'applicant_national_id', 'applicant_role',
            'applicant_phone', 'applicant_email', 'applicant_address'
          ]},
          { title: 'Land Information', fields: [
            'plot_number', 'land_title_number', 'land_size_sqm',
            'land_location_district', 'land_use_zoning', 'land_ownership_type'
          ]}
        ];
        
        sections.forEach(section => {
          const sectionDiv = document.createElement('div');
          sectionDiv.className = 'mb-4';
          sectionDiv.innerHTML = `<h6 class="fw-bold mb-3">${section.title}</h6>`;
          
          section.fields.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
              const label = input.previousElementSibling?.textContent || field;
              const value = input.value || 'Not provided';
              const reviewItem = document.createElement('div');
              reviewItem.className = 'review-item';
              reviewItem.innerHTML = `
                <div class="review-label">${label.replace('*', '').trim()}</div>
                <div class="review-value">${value}</div>
              `;
              sectionDiv.appendChild(reviewItem);
            }
          });
          
          reviewContent.appendChild(sectionDiv);
        });
        
        document.getElementById('formSection').style.display = 'none';
        document.getElementById('reviewSection').classList.add('active');
      } else {
        alert('Please fill in all required fields correctly before reviewing.');
      }
    });

    document.getElementById('editBtn').addEventListener('click', function() {
      document.getElementById('reviewSection').classList.remove('active');
      document.getElementById('formSection').style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('confirmSubmitBtn').addEventListener('click', function(e) {
      e.preventDefault();
      form.submit();
    });

    // Terms checkbox handler
    if (termsCheckbox && submitBtn) {
      submitBtn.disabled = !termsCheckbox.checked;
      termsCheckbox.addEventListener('change', () => {
        submitBtn.disabled = !termsCheckbox.checked;
      });
    }

    // Form submission - clear draft
    form.addEventListener('submit', function() {
      localStorage.removeItem(STORAGE_KEY);
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      loadDraft();
      updateProgress();
      window.addEventListener('beforeunload', saveDraft);
    });

    window.addEventListener('pageshow', function(event) {
      if (event.persisted) {
        loadDraft();
        updateProgress();
      }
    });
  </script>
  @stack('page-scripts')
</body>
</html>
