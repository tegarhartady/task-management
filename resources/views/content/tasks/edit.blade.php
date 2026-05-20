@php
$configData = Helper::appClasses();
$users = App\Models\User::where('is_active', true)->get();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Edit Task - ' . $task->title)

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Edit Task</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <!-- Title -->
          <div class="mb-4">
            <label class="form-label">Task Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Enter task title" required value="{{ old('title', $task->title) }}">
            @error('title')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Description -->
          <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="5" placeholder="Describe the task...">{{ old('description', $task->description) }}</textarea>
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
                <option value="Low" {{ old('priority', $task->priority) === 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ old('priority', $task->priority) === 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ old('priority', $task->priority) === 'High' ? 'selected' : '' }}>High</option>
              </select>
              @error('priority')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Assign To (Hanya untuk Admin/Supervisor/Manager) -->
            @if(!auth()->user()->isKaryawan())
              <div class="col-md-6">
                <label class="form-label">Assign To</label>
                <select class="form-select @error('assigned_to') is-invalid @enderror" name="assigned_to">
                  <option value="">Select User</option>
                  @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('assigned_to', $task->assigned_to) == $u->id ? 'selected' : '' }}>
                      {{ $u->name }} ({{ ucfirst($u->role) }})
                    </option>
                  @endforeach
                </select>
                @error('assigned_to')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            @endif
          </div>

          <!-- Due Date -->
          <div class="mb-4">
            <label class="form-label">Due Date</label>
            <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
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
              <!-- Existing Attachments -->
              @if($task->attachments->count() > 0)
                <div class="mb-4">
                  <h6 class="mb-3">Current Attachments</h6>
                  <div class="row" id="existingAttachments">
                    @foreach($task->attachments as $attachment)
                      <div class="col-md-4 mb-3" data-attachment-id="{{ $attachment->id }}">
                        <div class="card border position-relative">
                          <div class="card-body p-2">
                            @if($attachment->type === 'image')
                              <img src="{{ Storage::url($attachment->file_path) }}" class="img-fluid" style="max-height: 120px;">
                              <small class="d-block text-truncate mt-2">{{ $attachment->original_name }}</small>
                            @else
                              <div class="d-flex align-items-center justify-content-center" style="height: 120px; background: #f8f9fa;">
                                <i class="ti ti-link" style="font-size: 2rem; color: #7367f0;"></i>
                              </div>
                              <small class="d-block text-truncate mt-2 text-primary">{{ $attachment->link }}</small>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-attachment" data-id="{{ $attachment->id }}">
                              <i class="ti ti-trash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
                <hr>
              @endif

              <!-- Image Upload -->
              <div class="mb-4">
                <label class="form-label">Upload New Images</label>
                <div class="input-group">
                  <input type="file" class="form-control" id="imageInput" name="attachments[]" multiple accept="image/*">
                </div>
                <small class="text-muted d-block mt-2">You can select multiple images. Supported formats: JPG, PNG, GIF</small>
                <div id="imagePreview" class="row mt-3"></div>
              </div>

              <!-- Link Section -->
              <div>
                <label class="form-label">Add New Links</label>
                <textarea class="form-control" name="links" rows="3" placeholder="Enter links (one per line or comma-separated)&#10;e.g., https://example.com&#10;https://another-link.com"></textarea>
                <small class="text-muted d-block mt-2">Enter reference links for this task</small>
              </div>

              <!-- Hidden input for deleted attachments -->
              <input type="hidden" id="deletedAttachments" name="deleted_attachments" value="">
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-check me-1"></i> Update Task
            </button>
            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Track deleted attachments
const deletedAttachments = [];

// Delete button handler
document.querySelectorAll('.delete-attachment').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const id = this.dataset.id;
    const attachmentDiv = document.querySelector(`[data-attachment-id="${id}"]`);
    
    if (confirm('Are you sure you want to delete this attachment?')) {
      attachmentDiv.remove();
      deletedAttachments.push(id);
      document.getElementById('deletedAttachments').value = deletedAttachments.join(',');
    }
  });
});

// Image Preview for new uploads
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
