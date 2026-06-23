@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Create Reimbursement')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Back Link -->
  <div class="mb-4">
    <a href="{{ route('pages-reimburs') }}" class="text-primary text-decoration-none">
      <i class="ti ti-arrow-left me-2"></i>Back to Reimbursement
    </a>
  </div>

  <!-- Form Card -->
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Create New Reimbursement Request</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('reimburs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Title -->
            <div class="mb-3">
              <label class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('title') is-invalid @enderror" 
                name="title" placeholder="Enter reimbursement title" value="{{ old('title') }}" required>
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror" 
                name="description" rows="4" placeholder="Describe the expense details..."
                >{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Category -->
            <div class="mb-3">
              <label class="form-label">Category <span class="text-danger">*</span></label>
              <select class="form-select @error('category') is-invalid @enderror" 
                name="category" required>
                <option value="">Select Category</option>
                <option value="Transport" {{ old('category') == 'Transport' ? 'selected' : '' }}>Transport</option>
                <option value="Meal" {{ old('category') == 'Meal' ? 'selected' : '' }}>Meal</option>
                <option value="Office Supplies" {{ old('category') == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
              </select>
              @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Supervisor -->
            <div class="mb-3">
              <label class="form-label">Supervisor <span class="text-danger">*</span></label>
              <select class="form-select @error('supervisor_id') is-invalid @enderror" 
                name="supervisor_id" required>
                <option value="">Select Supervisor</option>
                @foreach($supervisors as $supervisor)
                  <option value="{{ $supervisor->id }}" {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                    {{ $supervisor->name }} ({{ $supervisor->email }})
                  </option>
                @endforeach
              </select>
              @error('supervisor_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Amount -->
            <div class="mb-3">
              <label class="form-label">Amount (Rp) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                  name="amount" placeholder="0" min="0" step="1000" value="{{ old('amount') }}" required>
              </div>
              @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Date -->
            <div class="mb-3">
              <label class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('date') is-invalid @enderror" 
                name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
              @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- File Attachments -->
            <div class="mb-3">
              <label class="form-label">Upload Proof (Images / PDF) <span class="text-danger">*</span></label>
              <div class="mb-2">
                <small class="text-muted d-block mb-2">
                  Supported: JPG, PNG, GIF, PDF (Max 5MB per file). You can upload multiple files.
                </small>
              </div>
              
              <!-- Drag & Drop Area -->
              <div class="card border-2 border-dashed text-center p-4" id="dropZone" 
                style="cursor: pointer; transition: all 0.3s ease;">
                <i class="ti ti-cloud-upload" style="font-size: 2.5rem; color: #7367f0;"></i>
                <p class="mt-2 mb-1"><strong>Drag files here or click to upload</strong></p>
                <small class="text-muted">or click to select files from your computer</small>
                <input type="file" id="fileInput" name="attachments[]" multiple 
                  class="d-none" accept=".pdf,.jpg,.jpeg,.png,.gif">
              </div>

              <!-- File Preview -->
              <div id="filePreview" class="mt-3"></div>

              @error('attachments.*')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
              @enderror
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-send me-2"></i>Submit Request
              </button>
              <a href="{{ route('pages-reimburs') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Info Card -->
    <div class="col-md-4">
      <div class="card bg-light">
        <div class="card-body">
          <h6 class="mb-3">
            <i class="ti ti-info-circle me-2"></i>Guidelines
          </h6>
          <ul class="ps-3 mb-0">
            <li class="mb-2"><strong>Be specific</strong> - Include details about the expense</li>
            <li class="mb-2"><strong>Upload proof</strong> - Receipt, invoice, or documentation</li>
            <li class="mb-2"><strong>Categories</strong> - Choose the correct category</li>
            <li class="mb-2"><strong>Double check</strong> - Review before submitting</li>
            <li><strong>Status</strong> - You'll get notification when approved/rejected</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('fileInput');
  const filePreview = document.getElementById('filePreview');

  // Click to upload
  dropZone.addEventListener('click', () => fileInput.click());

  // Drag & drop
  dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.backgroundColor = '#f3f3ff';
    dropZone.style.borderColor = '#7367f0';
  });

  dropZone.addEventListener('dragleave', () => {
    dropZone.style.backgroundColor = '';
    dropZone.style.borderColor = '';
  });

  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.backgroundColor = '';
    dropZone.style.borderColor = '';
    fileInput.files = e.dataTransfer.files;
    displayFiles();
  });

  // File input change
  fileInput.addEventListener('change', displayFiles);

  function displayFiles() {
    const files = fileInput.files;
    filePreview.innerHTML = '';

    if (files.length === 0) return;

    const container = document.createElement('div');
    container.className = 'row';

    Array.from(files).forEach((file, index) => {
      const col = document.createElement('div');
      col.className = 'col-6 col-md-12 mb-2';

      const preview = document.createElement('div');
      preview.className = 'card';
      preview.style.position = 'relative';

      let thumbnail = '';
      if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          const img = preview.querySelector('img');
          if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(file);
        thumbnail = `<img src="" style="width: 100%; height: 100px; object-fit: cover;">`;
      } else if (file.type === 'application/pdf') {
        thumbnail = `<div style="width: 100%; height: 100px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
          <i class="ti ti-file-pdf" style="font-size: 2rem; color: #dc3545;"></i>
        </div>`;
      } else {
        thumbnail = `<div style="width: 100%; height: 100px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
          <i class="ti ti-file" style="font-size: 2rem; color: #6c757d;"></i>
        </div>`;
      }

      preview.innerHTML = thumbnail + `
        <div class="card-body p-2">
          <small class="text-truncate d-block">${file.name}</small>
          <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
          <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeFile(${index})">
            <i class="ti ti-trash"></i> Remove
          </button>
        </div>
      `;

      container.appendChild(col);
      col.appendChild(preview);
    });

    filePreview.appendChild(container);
  }

  function removeFile(index) {
    const dt = new DataTransfer();
    const files = fileInput.files;

    for (let i = 0; i < files.length; i++) {
      if (i !== index) {
        dt.items.add(files[i]);
      }
    }

    fileInput.files = dt.files;
    displayFiles();
  }
</script>
@endsection
