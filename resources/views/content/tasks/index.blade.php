@php
$configData = Helper::appClasses();
$user = auth()->user();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Tasks')

@section('content')
<div class="row">
  <div class="col-12 d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Work Tasks</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">+ Create Task</button>
  </div>
</div>

<!-- Status count cards -->
<div class="row mb-3">
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 {{ $countOverdue > 0 ? 'bg-danger text-white' : '' }}">
      <div class="card-body py-3">
        <h3 class="mb-0 {{ $countOverdue > 0 ? 'text-white' : 'text-danger' }} fw-bold">{{ $countOverdue }}</h3>
        <small class="{{ $countOverdue > 0 ? 'text-white' : 'text-muted' }} fw-bold">OVERDUE</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0">{{ $countAll }}</h3>
        <small class="text-muted">All Tasks</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-success">{{ $countCompleted }}</h3>
        <small class="text-muted">Completed</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-warning">{{ $countInProgress }}</h3>
        <small class="text-muted">In Progress</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-primary">{{ $countPending }}</h3>
        <small class="text-muted">Pending Review</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-info">{{ $countApproved }}</h3>
        <small class="text-muted">Approved</small>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<form method="GET" class="mb-3">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">
          <select class="form-select w-auto" name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="Not Started" {{ request('status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
            <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Pending Review" {{ request('status') == 'Pending Review' ? 'selected' : '' }}>Pending Review</option>
            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
          <select class="form-select w-auto" name="priority" onchange="this.form.submit()">
            <option value="">All Priority</option>
            <option value="High" {{ request('priority') == 'High' ? 'selected' : '' }}>High</option>
            <option value="Medium" {{ request('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
            <option value="Low" {{ request('priority') == 'Low' ? 'selected' : '' }}>Low</option>
          </select>
          <div class="flex-grow-1">
            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search tasks..." onkeydown="if(event.key==='Enter'){this.form.submit();}">
          </div>
          <button type="submit" class="btn btn-outline-secondary">Filter</button>
          @if(request('status') || request('priority') || request('search'))
            <a href="{{ route('tasks.index') }}" class="btn btn-link">Reset</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Task List -->
<div class="row">
  <div class="col-12">
    @php
      $statusColors = [
        'Completed' => '#28c76f',
        'In Progress' => '#ff9f43',
        'Pending Review' => '#7367f0',
        'Rejected' => '#ea5455',
        'Approved' => '#00bcd4',
        'Not Started' => '#999',
      ];
    @endphp
    @if($tasks->count() > 0)
      @foreach($tasks as $task)
        @php
          $borderColor = $statusColors[$task->status] ?? '#ccc';
        @endphp
        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
          <div class="card mb-3" style="border-left:4px solid {{ $borderColor }}; cursor:pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
            <div class="card-body d-flex justify-content-between align-items-start">
              <div style="max-width:78%;">
                <h6 class="mb-1 text-body">{{ $task->title }}</h6>
                <p class="text-muted mb-2">{{ Str::limit($task->description, 80) }}</p>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge
                    @if($task->status=='Completed') bg-success
                    @elseif($task->status=='In Progress') bg-warning text-dark
                    @elseif($task->status=='Pending Review') bg-primary
                    @elseif($task->status=='Approved') bg-info
                    @else bg-danger @endif
                  ">{{ $task->status }}</span>
                  <span class="badge bg-secondary">{{ $task->priority }}</span>
                  @if($task->progress > 0)
                    <span class="badge bg-light text-dark">{{ $task->progress }}%</span>
                  @endif
                  @if($task->isOverdue())
                    <span class="badge bg-danger animate__animated animate__flash animate__infinite">OVERDUE</span>
                  @endif
                </div>
              </div>
              <div class="text-end text-muted" style="min-width:140px;">
                <div class="mb-2"><i class="ti ti-user me-1"></i> {{ $task->assignedTo?->name ?? 'Unassigned' }}</div>
                <div class="{{ $task->isOverdue() ? 'text-danger fw-bold' : '' }}">
                  <i class="ti ti-calendar me-1"></i> {{ $task->due_date?->format('d M Y') ?? 'N/A' }}
                </div>
              </div>
            </div>
          </div>
        </a>
      @endforeach

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
          Showing <strong>{{ ($tasks->currentPage() - 1) * $tasks->perPage() + 1 }}-{{ min($tasks->currentPage() * $tasks->perPage(), $tasks->total()) }}</strong> of <strong>{{ $tasks->total() }}</strong> tasks
        </div>
        <div>
          {{ $tasks->links() }}
        </div>
      </div>
    @else
      <div class="text-center py-5 text-muted">
        <i class="ti ti-file-off ti-lg mb-2 d-block"></i>
        <p>No tasks found</p>
      </div>
    @endif
  </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" id="createTaskForm">
        @csrf
        <div class="modal-body">
          <!-- Brief Selection -->
          <div class="mb-3">
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
              <small class="text-danger d-block mt-1">Anda tidak memiliki Brief yang aktif.</small>
            @endif
            @error('brief_id')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Title -->
          <div class="mb-3">
            <label class="form-label">Task Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Enter task title" required>
            @error('title')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Description -->
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" placeholder="Describe the task..."></textarea>
            @error('description')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="row">
            <!-- Priority -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Priority <span class="text-danger">*</span></label>
                <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                  <option value="">Select Priority</option>
                  <option value="Low">Low</option>
                  <option value="Medium" selected>Medium</option>
                  <option value="High">High</option>
                </select>
                @error('priority')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Assign To (Hanya untuk Admin/Supervisor/Manager) -->
            @if(!$user->isKaryawan())
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Assign To</label>
                  <select class="form-select @error('assigned_to') is-invalid @enderror" name="assigned_to">
                    <option value="">Select User</option>
                    @php
                      $users = \App\Models\User::where('is_active', true)->get();
                    @endphp
                    @foreach($users as $u)
                      <option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                    @endforeach
                  </select>
                  @error('assigned_to')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            @endif
          </div>

          <!-- Due Date -->
          <div class="mb-3">
            <label class="form-label">Due Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('due_date') is-invalid @enderror" name="due_date" required>
            @error('due_date')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- Attachments -->
          <div class="card border-light mb-3">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0">Attachments</h6>
            </div>
            <div class="card-body">
              <!-- Image Upload -->
              <div class="mb-3">
                <label class="form-label">Upload Images</label>
                <input type="file" class="form-control" id="imageInput" name="attachments[]" multiple accept="image/*">
                <small class="text-muted d-block mt-1">JPG, PNG, GIF</small>
                <div id="imagePreview" class="row mt-2"></div>
              </div>

              <!-- Links -->
              <div>
                <label class="form-label">Add Links</label>
                <textarea class="form-control" name="links" rows="2" placeholder="Enter links (comma or line separated)"></textarea>
                <small class="text-muted d-block mt-1">Reference links for this task</small>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-check me-1"></i> Create Task
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Image preview dalam modal
document.getElementById('imageInput')?.addEventListener('change', function(e) {
  const preview = document.getElementById('imagePreview');
  preview.innerHTML = '';

  Array.from(this.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = function(event) {
      const div = document.createElement('div');
      div.className = 'col-md-3 mb-2';
      div.innerHTML = `
        <div class="card border" style="padding: 4px;">
          <img src="${event.target.result}" class="img-fluid" style="max-height: 80px; object-fit: cover;">
          <small class="d-block text-truncate mt-1 px-1" title="${file.name}">${file.name}</small>
        </div>
      `;
      preview.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
});
</script>
@endsection
