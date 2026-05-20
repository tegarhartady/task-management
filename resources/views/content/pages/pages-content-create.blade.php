{{-- filepath: resources/views/content/pages/pages-content-create.blade.php --}}
@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Create New Brief')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Back Link -->
  <div class="mb-4">
    <a href="{{ url('pages-content') }}" class="text-decoration-none text-primary">
      <i class="ti ti-arrow-left me-2"></i>Back to Briefs
    </a>
  </div>

  <div class="row">
    <!-- Form Column -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Create New Brief</h5>
        </div>
        <div class="card-body">
          <form id="createBriefForm" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div class="mb-4">
              <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Enter brief title" required>
              <small class="text-muted">Give your brief a clear, descriptive title</small>
            </div>

            <!-- Brand -->
            <div class="mb-4">
              <label class="form-label" for="brand">Brand <span class="text-danger">*</span></label>
              <select class="form-select" id="brand" name="brand" required>
                <option value="">Select Brand</option>
                <option value="SUPERNATA">Supernata</option>
                <option value="DEKORNATA">Dekornata</option>
                <option value="CRAFTNATA">Craftnata</option>
              </select>
            </div>

            <!-- Type -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label" for="type">Content Type <span class="text-danger">*</span></label>
                <select class="form-select" id="type" name="type" required>
                  <option value="">Select Type</option>
                  <option value="REEL">REEL</option>
                  <option value="IGS">IGS (Instagram Story)</option>
                  <option value="POST">POST</option>
                  <option value="CAROUSEL">CAROUSEL</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="due_date">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date">
                <small class="text-muted">When should this content be ready?</small>
              </div>
            </div>

            <!-- Hook -->
            <div class="mb-4">
              <label class="form-label" for="hook">Hook (First 1-3 Seconds)</label>
              <input type="text" class="form-control" id="hook" name="hook" placeholder="What catches attention in the first seconds?">
              <small class="text-muted">Keep it short and punchy</small>
            </div>

            <!-- Concept / Story Arc -->
            <div class="mb-4">
              <label class="form-label" for="concept">Concept / Story Arc</label>
              <textarea class="form-control" id="concept" name="concept" rows="4" placeholder="Describe the main concept and story flow..."></textarea>
              <small class="text-muted">Explain the narrative and key message</small>
            </div>

            <!-- Visual Direction -->
            <div class="mb-4">
              <label class="form-label" for="visual_direction">Visual Direction</label>
              <textarea class="form-control" id="visual_direction" name="visual_direction" rows="4" placeholder="Describe colors, mood, composition, and visual elements..."></textarea>
              <small class="text-muted">Include mood, colors, camera angles, and styling</small>
            </div>

            <!-- Voiceover Script -->
            <div class="mb-4">
              <label class="form-label" for="voiceover">Voiceover Script</label>
              <textarea class="form-control" id="voiceover" name="voiceover" rows="4" placeholder="Write the voiceover script or dialogue..."></textarea>
              <small class="text-muted">Include all spoken content and tone notes</small>
            </div>

            <!-- Assign To (Multiple) -->
            <div class="mb-4">
              <label class="form-label" for="assignees">Assign to Employees (Karyawan)</label>
              <div class="d-flex mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary me-2" id="selectAllKaryawan">
                  <i class="ti ti-users me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllKaryawan">
                  <i class="ti ti-user-x me-1"></i>Deselect All
                </button>
              </div>
              <select class="form-select" id="assignees" name="assignees[]" multiple style="height: 150px;">
                @foreach($karyawan as $user)
                  <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
              </select>
              <small class="text-muted">Select one or more employees to assign this content to</small>
            </div>

            <!-- File Upload -->
            <div class="mb-4">
              <label class="form-label">Reference Images or Documents</label>
              <div class="border-2 border-dashed rounded p-4 text-center" id="dropZone" style="cursor: pointer; transition: all 0.3s ease;">
                <i class="ti ti-cloud-upload" style="font-size: 2.5rem; color: #7367f0;"></i>
                <p class="mt-3 mb-0"><strong>Drag and drop files here</strong></p>
                <small class="text-muted d-block mt-1">or</small>
                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="document.getElementById('fileInput').click()">
                  Select Files
                </button>
                <input type="file" id="fileInput" name="files[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" style="display: none;">
                <small class="text-muted d-block mt-2">Supported: JPG, PNG, PDF, Word, Excel, PPT (Max 10MB each)</small>
              </div>

              <!-- File Preview -->
              <div id="filePreview" class="mt-3" style="display: none;">
                <h6 class="mb-3">Selected Files:</h6>
                <div id="fileList" class="row g-2">
                  <!-- Files will be added here -->
                </div>
              </div>
            </div>

            <!-- AI Generated Toggle -->
            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="isAI" name="is_ai">
                <label class="form-check-label" for="isAI">
                  <i class="ti ti-robot me-1"></i>This brief was generated with AI assistance
                </label>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-1"></i>Create Brief
              </button>
              <a href="{{ url('pages-content') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Guidelines Column -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0"><i class="ti ti-info-circle me-2"></i>Guidelines</h5>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <h6 class="mb-2">Best Practices</h6>
            <ul class="ps-3 mb-0">
              <li><strong>Hook:</strong> Grab attention in 1-3 seconds</li>
              <li><strong>Story:</strong> Have a clear narrative arc</li>
              <li><strong>Visuals:</strong> Be specific with details</li>
              <li><strong>Script:</strong> Write for the voice & tone</li>
            </ul>
          </div>

          <div class="mb-4">
            <h6 class="mb-2">Content Tips</h6>
            <ul class="ps-3 mb-0">
              <li>Keep hook text under 10 words</li>
              <li>Concept should be 50-150 words</li>
              <li>Visual direction: describe in detail</li>
              <li>Include emotion & brand voice in copy</li>
            </ul>
          </div>

          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="ti ti-lightbulb me-2"></i>
            <strong>Tip:</strong> Upload reference images to help the team visualize your concept better.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>

          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            <strong>Remember:</strong> All briefs will be reviewed before approval.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Include Quill Editor -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Select All Karyawan logic
  const selectAllBtn = document.getElementById('selectAllKaryawan');
  const deselectAllBtn = document.getElementById('deselectAllKaryawan');
  const assigneesSelect = document.getElementById('assignees');

  if (selectAllBtn) {
    selectAllBtn.addEventListener('click', function() {
      Array.from(assigneesSelect.options).forEach(option => {
        option.selected = true;
      });
    });
  }

  if (deselectAllBtn) {
    deselectAllBtn.addEventListener('click', function() {
      Array.from(assigneesSelect.options).forEach(option => {
        option.selected = false;
      });
    });
  }

  // File Upload handling
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('fileInput');
  const filePreview = document.getElementById('filePreview');
  const fileList = document.getElementById('fileList');

  // Prevent default drag behaviors
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
  });

  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  // Highlight drop zone when dragging
  ['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
      dropZone.style.backgroundColor = '#f5f3ff';
      dropZone.style.borderColor = '#7367f0';
    });
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
      dropZone.style.backgroundColor = 'transparent';
      dropZone.style.borderColor = '#dee2e6';
    });
  });

  // Handle file drop - set files to input
  dropZone.addEventListener('drop', function(e) {
    fileInput.files = e.dataTransfer.files;
    displayFiles();
  }, false);

  // Handle file input change
  fileInput.addEventListener('change', function() {
    displayFiles();
  });

  function displayFiles() {
    fileList.innerHTML = '';
    const files = fileInput.files;

    if (files.length === 0) {
      filePreview.style.display = 'none';
      return;
    }

    Array.from(files).forEach((file, index) => {
      const div = document.createElement('div');
      div.className = 'col-6 col-md-4';

      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          div.innerHTML = `
            <div class="position-relative">
              <img src="${e.target.result}" class="img-fluid rounded" style="object-fit: cover; height: 150px; width: 100%;">
              <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeFile(${index})">
                <i class="ti ti-x"></i>
              </button>
            </div>
          `;
        };
        reader.readAsDataURL(file);
      } else {
        let icon = 'ti-file';
        let color = '#7367f0';
        
        if (file.type === 'application/pdf' || file.name.endsWith('.pdf')) {
          icon = 'ti-file-pdf';
          color = '#dc3545';
        } else if (file.name.match(/\.(doc|docx)$/i)) {
          icon = 'ti-file-description';
          color = '#007bff';
        } else if (file.name.match(/\.(xls|xlsx)$/i)) {
          icon = 'ti-file-spreadsheet';
          color = '#28a745';
        }

        div.innerHTML = `
          <div class="border rounded p-3 text-center h-100 position-relative d-flex flex-column justify-content-center align-items-center">
            <i class="ti ${icon}" style="font-size: 2rem; color: ${color};"></i>
            <small class="mt-2 text-truncate w-100">${file.name}</small>
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeFile(${index})">
              <i class="ti ti-x"></i>
            </button>
          </div>
        `;
      }

      fileList.appendChild(div);
    });

    filePreview.style.display = files.length > 0 ? 'block' : 'none';
  }

  window.removeFile = function(index) {
    const dt = new DataTransfer();
    const files = fileInput.files;
    for (let i = 0; i < files.length; i++) {
      if (i !== index) dt.items.add(files[i]);
    }
    fileInput.files = dt.files;
    displayFiles();
  };

  // Form submission
  document.getElementById('createBriefForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    console.log('DEBUG - FormData entries:');
    for (let pair of formData.entries()) {
      console.log(pair[0] + ': ' + (pair[1].constructor.name === 'File' ? pair[1].name : pair[1].substring(0, 50)));
    }

    // Submit to server
    fetch('{{ route("pages-content.store") }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    })
    .then(response => {
      if (response.ok) {
        window.location.href = '{{ url("pages-content") }}';
      } else {
        return response.json().then(data => {
          alert('Error: ' + (data.message || 'Failed to create brief'));
        });
      }
    })
    .catch(error => {
      alert('Error: ' + error.message);
    });
  });
});
</script>

<style>
  .ql-container {
    font-size: 1rem;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0 0 4px 4px;
  }

  .ql-editor {
    min-height: 200px;
    padding: 12px;
  }

  .ql-toolbar {
    border: 1px solid #dee2e6;
    border-radius: 4px 4px 0 0;
    background-color: #f8f9fa;
  }

  .border-2 {
    border-width: 2px !important;
  }

  .border-dashed {
    border-style: dashed !important;
  }

  @media (max-width: 768px) {
    .col-lg-8,
    .col-lg-4 {
      margin-bottom: 2rem;
    }
  }
</style>

@endsection
