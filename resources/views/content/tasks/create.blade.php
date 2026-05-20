@php
$configData = Helper::appClasses();
$users = App\Models\User::where('is_active', true)->get();
$currentUser = auth()->user();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Create Task')

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Create New Task</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <!-- Brief Selection -->
          <div class="mb-4">
            <label class="form-label">Pilih Brief <span class="text-danger">*</span></label>
            <select class="form-select @error('brief_id') is-invalid @enderror" name="brief_id" required>
              <option value="">-- Pilih Brief --</option>
              @foreach($myBriefs as $brief)
                <option value="{{ $brief->id }}" {{ old('brief_id') == $brief->id ? 'selected' : '' }}>
                  {{ $brief->title }} {{ $brief->due_date ? '(Deadline: ' . $brief->due_date->format('Y-m-d') . ')' : '' }}
                </option>
              @endforeach
            </select>
            @if($myBriefs->isEmpty())
              <small class="text-danger d-block mt-1">Anda tidak memiliki Brief yang aktif. Silahkan hubungi Atasan.</small>
            @endif
            @error('brief_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Title -->
          <div class="mb-4">
            <label class="form-label">Task Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Enter task title" required value="{{ old('title') }}">
            @error('title')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Description -->
          <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="5" placeholder="Describe the task...">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="row mb-4">
            <!-- Priority -->
            <div class="col-md-6">
              <label class="form-label">Priority <span class="text-danger">*</span></label>
              <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                <option value="">Select Priority</option>
                <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ old('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High</option>
              </select>
              @error('priority')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Assign To -->
            <div class="col-md-6">
              <label class="form-label">Assign To</label>
              <select class="form-select @error('assigned_to') is-invalid @enderror" name="assigned_to">
                <option value="">Select User</option>
                @foreach($users as $u)
                  <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>
                    {{ $u->name }} ({{ ucfirst($u->role) }})
                  </option>
                @endforeach
              </select>
              @if($currentUser->isKaryawan())
                <small class="text-muted d-block mt-1">If not assigned, will be assigned to you automatically</small>
              @endif
              @error('assigned_to')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Due Date -->
          <div class="mb-4">
            <label class="form-label">Due Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" required value="{{ old('due_date') }}">
            @error('due_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Attachments Section -->
          <div class="card border-light mb-4">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0">Attachments</h6>
            </div>
            <div class="card-body">
              <!-- Image Upload -->
              <div class="mb-4">
                <label class="form-label">Upload Images</label>
                <div class="input-group">
                  <input type="file" class="form-control" id="imageInput" name="attachments[]" multiple accept="image/*">
                </div>
                <small class="text-muted d-block mt-2">You can select multiple images. Supported formats: JPG, PNG, GIF</small>
                <div id="imagePreview" class="row mt-3"></div>
              </div>

              <!-- Link Section -->
              <div>
                <label class="form-label">Add Links</label>
                <textarea class="form-control" name="links" rows="3" placeholder="Enter links (one per line or comma-separated)&#10;e.g., https://example.com&#10;https://another-link.com"></textarea>
                <small class="text-muted d-block mt-2">Enter reference links for this task</small>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-check me-1"></i> Create Task
            </button>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Image Preview
document.getElementById('imageInput').addEventListener('change', function(e) {
  const preview = document.getElementById('imagePreview');
  preview.innerHTML = '';
  
  Array.from(this.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = function(event) {
      const div = document.createElement('div');
      div.className = 'col-md-4 mb-3';
      div.innerHTML = `
        <div class="card border">
          <div class="card-body p-2">
            <img src="${event.target.result}" class="img-fluid" style="max-height: 120px;">
            <small class="d-block text-truncate mt-2">${file.name}</small>
          </div>
        </div>
      `;
      preview.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
});
</script>
@endsection
