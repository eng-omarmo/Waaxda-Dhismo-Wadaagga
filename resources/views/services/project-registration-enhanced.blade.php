<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Registration – IPAMS</title>
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
    /* Accessibility improvements */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border-width: 0;
    }
    .form-control:invalid, .form-select:invalid {
      border-color: #dc3545;
    }
    .form-control:valid:not(:placeholder-shown), .form-select:valid {
      border-color: #28a745;
    }
    /* Tooltip styling */
    [data-bs-toggle="tooltip"] {
      cursor: help;
    }
    /* Focus indicators for keyboard navigation */
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
      <h1 class="fw-extrabold mb-2">Project Registration</h1>
      <p class="mb-0">Complete the form below to register your construction project. All fields marked with <span class="required">*</span> are required.</p>
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
            <form id="projectRegistrationForm" method="POST" action="{{ route('services.project-registration.store') }}" novalidate>
              @csrf
              
              <!-- Section 1: Contact Information -->
              <h5 class="section-heading">
                <i class="bi bi-person-circle me-2"></i>Contact Information
              </h5>
              
              <div class="row g-3">
                <div class="col-md-6 field-group">
                  <label for="registrant_name" class="form-label">
                    Full Name <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Enter your full legal name as it appears on official documents"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="registrant_name" 
                    name="registrant_name" 
                    placeholder="e.g., Mohamed Ali Hassan"
                    value="{{ old('registrant_name', '') }}"
                    required
                    aria-describedby="registrant_name_help registrant_name_error"
                    autocomplete="name">
                  <div class="form-text-help" id="registrant_name_help">
                    Use your legal name for official records
                  </div>
                  <div class="form-error" id="registrant_name_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="registrant_phone" class="form-label">
                    Phone Number <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Enter a valid phone number where we can reach you"></i>
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

                <div class="col-md-12 field-group">
                  <label for="registrant_email" class="form-label">
                    Email Address <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="We'll send confirmation and updates to this email address"></i>
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
                    Enter a valid email address for notifications
                  </div>
                  <div class="form-error" id="registrant_email_error" role="alert"></div>
                </div>
              </div>

              <!-- Section 2: Project Details -->
              <h5 class="section-heading mt-4">
                <i class="bi bi-building me-2"></i>Project Details
              </h5>

              <div class="row g-3">
                <div class="col-md-12 field-group">
                  <label for="project_name" class="form-label">
                    Project Name <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Give your project a clear, descriptive name"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="project_name" 
                    name="project_name" 
                    placeholder="e.g., Daru Salaam Apartments Phase II"
                    value="{{ old('project_name', '') }}"
                    required
                    aria-describedby="project_name_help project_name_error"
                    autocomplete="off">
                  <div class="form-text-help" id="project_name_help">
                    Use a name that clearly identifies your construction project
                  </div>
                  <div class="form-error" id="project_name_error" role="alert"></div>
                </div>

                <div class="col-md-12 field-group">
                  <label for="location_text" class="form-label">
                    Project Location <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Provide the full address or location description of the project site"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="location_text" 
                    name="location_text" 
                    placeholder="District, neighborhood, street name, or map reference"
                    value="{{ old('location_text', '') }}"
                    required
                    aria-describedby="location_text_help location_text_error"
                    autocomplete="street-address">
                  <div class="form-text-help" id="location_text_help">
                    Be as specific as possible: district, neighborhood, and street if available
                  </div>
                  <div class="form-error" id="location_text_error" role="alert"></div>
                </div>
              </div>

              <!-- Hidden Status Field -->
              <input type="hidden" name="status" id="statusField" value="Draft">

              <!-- Action Buttons -->
              <div class="btn-group-actions">
                <button type="submit" class="btn btn-primary" id="saveDraftBtn">
                  <i class="bi bi-save me-2"></i>Save Draft
                </button>
                <button type="button" class="btn btn-outline-primary" id="reviewBtn">
                  <i class="bi bi-eye me-2"></i>Review Before Submit
                </button>
                <button type="button" class="btn btn-success" id="submitBtn">
                  <i class="bi bi-send me-2"></i>Submit Registration
                </button>
              </div>
            </form>
          </div>

          <!-- Review Section -->
          <div id="reviewSection" class="review-section form-section">
            <h5 class="section-heading">
              <i class="bi bi-check-circle me-2"></i>Review Your Information
            </h5>
            <p class="text-muted mb-4">Please review all information before submitting. Click "Edit" to make changes.</p>
            
            <div class="review-item">
              <div class="review-label">Full Name</div>
              <div class="review-value" id="review_registrant_name"></div>
            </div>
            <div class="review-item">
              <div class="review-label">Phone Number</div>
              <div class="review-value" id="review_registrant_phone"></div>
            </div>
            <div class="review-item">
              <div class="review-label">Email Address</div>
              <div class="review-value" id="review_registrant_email"></div>
            </div>
            <div class="review-item">
              <div class="review-label">Project Name</div>
              <div class="review-value" id="review_project_name"></div>
            </div>
            <div class="review-item">
              <div class="review-label">Project Location</div>
              <div class="review-value" id="review_location_text"></div>
            </div>

            <div class="btn-group-actions">
              <button type="button" class="btn btn-outline-secondary" id="editBtn">
                <i class="bi bi-pencil me-2"></i>Edit Information
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
                <i class="bi bi-question-circle me-2"></i>Need Help?
              </h6>
              <p class="card-text text-muted mb-2">All services attach to projects. Register the project first to proceed with permits, buildings, units, licensing, ownership and transfers.</p>
              <hr>
              <div class="list-group list-group-flush">
                <div class="list-group-item border-0 px-0">
                  <small class="d-flex justify-content-between align-items-center">
                    <span>Construction Permits</span>
                    <span class="badge bg-light text-primary">After submission</span>
                  </small>
                </div>
                <div class="list-group-item border-0 px-0">
                  <small class="d-flex justify-content-between align-items-center">
                    <span>Buildings & Units</span>
                    <span class="badge bg-light text-primary">After approval</span>
                  </small>
                </div>
                <div class="list-group-item border-0 px-0">
                  <small class="d-flex justify-content-between align-items-center">
                    <span>Licensing</span>
                    <span class="badge bg-light text-primary">After approval</span>
                  </small>
                </div>
                <div class="list-group-item border-0 px-0">
                  <small class="d-flex justify-content-between align-items-center">
                    <span>Ownership & Transfers</span>
                    <span class="badge bg-light text-primary">After unit creation</span>
                  </small>
                </div>
              </div>
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
    // Auto-save functionality
    const STORAGE_KEY = 'project_registration_draft';
    const form = document.getElementById('projectRegistrationForm');
    const formFields = ['registrant_name', 'registrant_phone', 'registrant_email', 'project_name', 'location_text'];
    
    // Load saved draft on page load
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

    // Save draft to localStorage
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

    // Validate all fields
    function validateForm() {
      let isValid = true;
      formFields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
          if (!validateField(input)) {
            isValid = false;
          }
        }
      });
      return isValid;
    }

    // Update progress indicator
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

    // Review functionality
    document.getElementById('reviewBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        // Populate review section
        formFields.forEach(field => {
          const input = document.getElementById(field);
          const reviewDiv = document.getElementById('review_' + field);
          if (input && reviewDiv) {
            reviewDiv.textContent = input.value || 'Not provided';
          }
        });
        
        // Show review, hide form
        document.getElementById('formSection').style.display = 'none';
        document.getElementById('reviewSection').classList.add('active');
      } else {
        alert('Please fill in all required fields correctly before reviewing.');
      }
    });

    document.getElementById('editBtn').addEventListener('click', function() {
      document.getElementById('reviewSection').classList.remove('active');
      document.getElementById('formSection').style.display = 'block';
      // Scroll to top
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('submitBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        document.getElementById('statusField').value = 'Submitted';
        form.submit();
      }
    });

    document.getElementById('confirmSubmitBtn').addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('statusField').value = 'Submitted';
      form.submit();
    });

    // Form submission - clear draft
    form.addEventListener('submit', function() {
      localStorage.removeItem(STORAGE_KEY);
    });

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      // Load draft on page load
      loadDraft();
      updateProgress();
      
      // Auto-save on page unload as backup
      window.addEventListener('beforeunload', saveDraft);
    });

    // Handle browser back/forward navigation
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
