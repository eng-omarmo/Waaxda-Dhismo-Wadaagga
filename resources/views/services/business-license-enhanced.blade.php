<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Business License Application – IPAMS</title>
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
      <h1 class="fw-extrabold mb-2">Business License Application</h1>
      <p class="mb-0">Apply for a business license linked to your project. All fields marked with <span class="required">*</span> are required.</p>
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

          @if (session('status'))
            <div class="alert alert-success" role="alert">
              <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            </div>
          @endif

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
            <form id="businessLicenseForm" method="POST" action="{{ route('services.business-license.store') }}" enctype="multipart/form-data" novalidate>
              @csrf

              <!-- Section 1: Business Information -->
              <h5 class="section-heading">
                <i class="bi bi-building me-2"></i>Business Information
              </h5>

              <div class="row g-3">
                <div class="col-md-12 field-group">
                  <label for="company_name" class="form-label">
                    Company/Business Name <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Enter the official name of your business or company"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="company_name" 
                    name="company_name" 
                    placeholder="Company or business name"
                    value="{{ old('company_name', '') }}"
                    required
                    aria-describedby="company_name_help company_name_error"
                    autocomplete="organization">
                  <div class="form-text-help" id="company_name_help">
                    Use the official registered business name
                  </div>
                  <div class="form-error" id="company_name_error" role="alert"></div>
                </div>

                <div class="col-md-12 field-group">
                  <label for="project_id" class="form-label">
                    Project ID (Optional)
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Link this license to an existing project by entering the project UUID"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="project_id" 
                    name="project_id" 
                    placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                    value="{{ old('project_id', '') }}"
                    pattern="^[0-9a-fA-F\-]{36}$"
                    aria-describedby="project_id_help project_id_error"
                    autocomplete="off">
                  <div class="form-text-help" id="project_id_help">
                    Optional: Enter project UUID if this license is linked to a specific project
                  </div>
                  <div class="form-error" id="project_id_error" role="alert"></div>
                </div>

                <div class="col-md-12 field-group">
                  <label for="license_type" class="form-label">
                    License Type <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Select the type of business license you need"></i>
                  </label>
                  <select 
                    class="form-select" 
                    id="license_type" 
                    name="license_type" 
                    required
                    aria-describedby="license_type_help license_type_error">
                    <option value="">Select license type...</option>
                    <option value="Rental" {{ old('license_type') == 'Rental' ? 'selected' : '' }}>Rental</option>
                    <option value="Commercial" {{ old('license_type') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                  </select>
                  <div class="form-text-help" id="license_type_help">
                    Rental: For property rental businesses. Commercial: For commercial operations.
                  </div>
                  <div class="form-error" id="license_type_error" role="alert"></div>
                </div>
              </div>

              <!-- Section 2: Registrant Contact -->
              <h5 class="section-heading mt-4">
                <i class="bi bi-person-circle me-2"></i>Contact Information
              </h5>

              <div class="row g-3">
                <div class="col-md-6 field-group">
                  <label for="registrant_name" class="form-label">
                    Full Name <span class="required">*</span>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="registrant_name" 
                    name="registrant_name" 
                    placeholder="Your full name"
                    value="{{ old('registrant_name', '') }}"
                    required
                    aria-describedby="registrant_name_help registrant_name_error"
                    autocomplete="name">
                  <div class="form-text-help" id="registrant_name_help">
                    Name of person submitting this application
                  </div>
                  <div class="form-error" id="registrant_name_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="registrant_email" class="form-label">
                    Email Address <span class="required">*</span>
                  </label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="registrant_email" 
                    name="registrant_email" 
                    placeholder="you@example.com"
                    value="{{ old('registrant_email', '') }}"
                    required
                    aria-describedby="registrant_email_help registrant_email_error"
                    autocomplete="email">
                  <div class="form-text-help" id="registrant_email_help">
                    Email for notifications and updates
                  </div>
                  <div class="form-error" id="registrant_email_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="registrant_phone" class="form-label">
                    Phone Number <span class="required">*</span>
                  </label>
                  <input 
                    type="tel" 
                    class="form-control" 
                    id="registrant_phone" 
                    name="registrant_phone" 
                    placeholder="0612345678"
                    value="{{ old('registrant_phone', '') }}"
                    required
                    pattern="[0-9]{9,12}"
                    aria-describedby="registrant_phone_help registrant_phone_error"
                    autocomplete="tel">
                  <div class="form-text-help" id="registrant_phone_help">
                    Format: 9-12 digits without spaces or dashes
                  </div>
                  <div class="form-error" id="registrant_phone_error" role="alert"></div>
                </div>
              </div>

              <!-- Section 3: Documents -->
              <h5 class="section-heading mt-4">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>Supporting Documents
              </h5>

              <div class="field-group">
                <label for="documents" class="form-label">
                  Upload Documents <span class="required">*</span>
                  <i class="bi bi-info-circle help-icon" 
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top"
                     title="Upload business registration documents, certificates, and other supporting files"></i>
                </label>
                <input 
                  type="file" 
                  class="form-control" 
                  id="documents" 
                  name="documents[]" 
                  multiple 
                  accept=".pdf,.jpg,.jpeg,.png"
                  required
                  aria-describedby="documents_help documents_error">
                <div class="form-text-help" id="documents_help">
                  Upload multiple files (PDF, JPG, PNG). Max 5MB per file. At least one document is required.
                </div>
                <div id="fileList" class="file-list"></div>
                <div class="form-error" id="documents_error" role="alert"></div>
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
              <p class="text-muted mb-2">The license is linked to a project via project_id. Choose the appropriate license type: Rental or Commercial.</p>
              <hr>
              <ul class="small text-muted ps-3 mb-0">
                <li>Rental license: For businesses renting out properties</li>
                <li>Commercial license: For general commercial business operations</li>
                <li>Project ID is optional but recommended for better record management</li>
              </ul>
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
    const STORAGE_KEY = 'business_license_draft';
    const form = document.getElementById('businessLicenseForm');
    const formFields = [
      'company_name', 'project_id', 'license_type',
      'registrant_name', 'registrant_email', 'registrant_phone'
    ];

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
      
      // Check file upload
      const fileInput = document.getElementById('documents');
      if (fileInput && !fileInput.files || fileInput.files.length === 0) {
        isValid = false;
        const errorDiv = document.getElementById('documents_error');
        if (errorDiv) {
          errorDiv.classList.add('show');
          errorDiv.textContent = 'At least one document is required';
        }
      }
      
      return isValid;
    }

    // Update progress
    function updateProgress() {
      let filled = 0;
      const total = formFields.length + 1; // +1 for file upload
      
      formFields.forEach(field => {
        const input = document.getElementById(field);
        if (input && input.value.trim()) {
          filled++;
        }
      });
      
      // Check file upload
      const fileInput = document.getElementById('documents');
      if (fileInput && fileInput.files && fileInput.files.length > 0) {
        filled++;
      }
      
      const percentage = (filled / total) * 100;
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
        updateProgress();
      });
    }

    // Review functionality
    document.getElementById('reviewBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        const reviewContent = document.getElementById('reviewContent');
        reviewContent.innerHTML = '';
        
        const sections = [
          { title: 'Business Information', fields: [
            'company_name', 'project_id', 'license_type'
          ]},
          { title: 'Contact Information', fields: [
            'registrant_name', 'registrant_email', 'registrant_phone'
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
        
        // Add file count
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
          const fileReview = document.createElement('div');
          fileReview.className = 'review-item';
          fileReview.innerHTML = `
            <div class="review-label">Documents</div>
            <div class="review-value">${fileInput.files.length} file(s) selected</div>
          `;
          reviewContent.appendChild(fileReview);
        }
        
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
