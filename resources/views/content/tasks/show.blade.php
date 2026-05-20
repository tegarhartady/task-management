@php
$configData = Helper::appClasses();
$user = auth()->user();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Task - ' . $task->title)

@section('content')
<div class="row">
  <!-- Task Info Section -->
  <div class="col-lg-8 mb-4">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ $task->title }}</h5>
        <div>
          @if($task->isOverdue())
            <span class="badge bg-danger animate__animated animate__flash animate__infinite">OVERDUE</span>
          @endif
          <span class="badge
            @if($task->priority === 'High') bg-danger
            @elseif($task->priority === 'Medium') bg-warning
            @else bg-info @endif
          ">{{ $task->priority }}</span>
          <span class="badge
            @if($task->status === 'Completed') bg-success
            @elseif($task->status === 'Rejected') bg-danger
            @elseif($task->status === 'Pending Review') bg-warning
            @elseif($task->status === 'Approved') bg-info
            @else bg-secondary @endif
          ">{{ $task->status }}</span>
        </div>
      </div>
      <div class="card-body">
        @if($task->isOverdue())
          <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            <div>
              <strong>Overdue Notice:</strong> This task has passed its due date ({{ $task->due_date->format('d M Y') }}). Please complete it as soon as possible.
            </div>
          </div>
        @endif
        <!-- Description -->
        <h6 class="mb-2">Description</h6>
        <p class="text-muted mb-4">{{ $task->description }}</p>

        <!-- Task Details -->
        <div class="row mb-4">
          <div class="col-md-6 mb-3">
            <small class="text-muted d-block">Related Brief</small>
            <strong>{{ $task->brief ? $task->brief->title : 'N/A' }}</strong>
          </div>
          <div class="col-md-6 mb-3">
            <small class="text-muted d-block">Created By</small>
            <strong>{{ $task->creator->name }}</strong>
          </div>
          <div class="col-md-6 mb-3">
            <small class="text-muted d-block">Assigned To</small>
            <strong>{{ $task->assignedTo?->name ?? 'Unassigned' }}</strong>
          </div>
          <div class="col-md-6 mb-3">
            <small class="text-muted d-block">Due Date</small>
            <strong class="{{ $task->isOverdue() ? 'text-danger' : '' }}">{{ $task->due_date?->format('d M Y') ?? 'N/A' }}</strong>
          </div>
          <div class="col-md-6 mb-3">
            <small class="text-muted d-block">Progress</small>
            <div class="progress" style="height: 20px;">
              <div class="progress-bar" role="progressbar" style="width: {{ $task->progress }}%;" aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100">
                {{ $task->progress }}%
              </div>
            </div>
          </div>
        </div>

        <!-- Check In/Out Section -->
        @if($task->assignedTo?->id === $user->id && in_array($task->status, ['Not Started', 'In Progress']))
          <div class="alert alert-info mb-4">
            @if($task->isCheckedIn())
              <p>✓ You are currently checked in</p>
              <p><small>Started: {{ $task->checked_in_at->format('d M Y H:i') }}</small></p>
              <form action="{{ route('tasks.check-out', $task) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm">
                  <i class="ti ti-clock-off me-1"></i> Check Out
                </button>
              </form>
            @elseif($task->canCheckIn())
              <form action="{{ route('tasks.check-in', $task) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="ti ti-clock-play me-1"></i> Check In to Start Work
                </button>
              </form>
            @endif
          </div>
        @endif

        <!-- Attachments Section -->
        @if($task->attachments->count() > 0)
          <h6 class="mb-3 fw-bold">Attachments & Documents</h6>
          <div class="row g-3 mb-4">
            @foreach($task->attachments as $attachment)
              <div class="col-md-6 col-xl-4">
                <div class="card h-100 shadow-none border overflow-hidden attachment-card">
                  <div class="card-body p-0">
                    @if($attachment->type === 'image')
                      <div class="image-preview-container position-relative" style="height: 160px; overflow: hidden; background: #eee; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#imagePreviewModal"
                        data-img-src="{{ asset('storage/' . $attachment->file_path) }}"
                        data-img-title="{{ $attachment->original_name }}">
                        <img src="{{ asset('storage/' . $attachment->file_path) }}"
                          class="w-100 h-100 object-fit-cover transition-all"
                          alt="{{ $attachment->original_name }}">
                        <div class="image-overlay d-flex align-items-center justify-content-center position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 opacity-0 transition-all">
                          <i class="ti ti-zoom-in text-white fs-2"></i>
                        </div>
                      </div>
                    @else
                      <div class="d-flex align-items-center justify-content-center bg-label-primary" style="height: 160px;">
                        <i class="ti ti-link fs-1"></i>
                      </div>
                    @endif
                    <div class="p-3">
                      @if($attachment->type === 'link')
                        <a href="{{ $attachment->link }}" target="_blank" class="text-primary fw-bold text-truncate d-block mb-1" title="{{ $attachment->link }}">
                          <i class="ti ti-external-link me-1"></i> {{ Str::limit($attachment->link, 25) }}
                        </a>
                      @else
                        <h6 class="mb-1 text-truncate fw-bold" title="{{ $attachment->original_name }}">{{ $attachment->original_name }}</h6>
                      @endif
                      <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">by {{ $attachment->uploadedBy->name }}</small>
                        @if($attachment->type === 'image')
                          <a href="{{ asset('storage/' . $attachment->file_path) }}" download class="btn btn-sm btn-icon btn-label-secondary">
                            <i class="ti ti-download"></i>
                          </a>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- Activity/Comments Section - Chat Like -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="ti ti-message-circle me-2"></i> Activity & Comments
        </h5>
      </div>
      <div class="card-body" style="max-height: 500px; overflow-y: auto; background: #f8f9fa;">
        <!-- Comments List -->
        @forelse($task->comments as $comment)
          <div class="d-flex mb-3">
            <div class="flex-shrink-0 me-3">
              <div class="avatar">
                <span class="avatar-initial bg-label-primary rounded-circle">
                  {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </span>
              </div>
            </div>
            <div class="flex-grow-1">
              <div class="card border mb-2">
                <div class="card-body py-2 px-3">
                  <div class="d-flex justify-content-between mb-1">
                    <strong class="small">{{ $comment->user->name }}</strong>
                    <small class="text-muted">{{ $comment->created_at->format('d M H:i') }}</small>
                  </div>

                  @if($comment->type === 'status_change')
                    <small class="badge bg-info">{{ $comment->comment }}</small>
                  @elseif($comment->type === 'approval')
                    <small class="badge bg-success">✓ Approved</small>
                    <p class="mb-0 mt-2">{{ $comment->comment }}</p>
                  @elseif($comment->type === 'rejection')
                    <small class="badge bg-danger">✗ Rejected</small>
                    <p class="mb-0 mt-2">{{ $comment->comment }}</p>
                  @else
                    <p class="mb-0">{{ $comment->comment }}</p>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <p class="text-muted text-center py-4">No comments yet</p>
        @endforelse
      </div>

      <!-- Add Comment Form -->
      <div class="card-footer border-top">
        @if($task->assignedTo?->id === $user->id || $user->isSupervisor() || $user->isAdmin() || $task->created_by === $user->id)
          <form action="{{ route('tasks.comment', $task) }}" method="POST">
            @csrf
            <div class="input-group">
              <input type="text" class="form-control" name="comment" placeholder="Add a comment..." required>
              <button class="btn btn-primary" type="submit">
                <i class="ti ti-send"></i>
              </button>
            </div>
          </form>
        @else
          <p class="text-muted text-center mb-0"><small>You don't have permission to comment on this task</small></p>
        @endif
      </div>
    </div>
  </div>

  <!-- Sidebar - Actions -->
  <div class="col-lg-4">
    <!-- Action Buttons -->
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="card-title mb-0">Actions</h6>
      </div>
      <div class="card-body">
        @if($task->assignedTo?->id === $user->id && $task->status === 'In Progress' && !$task->isCheckedIn())
          <form action="{{ route('tasks.submit-review', $task) }}" method="POST" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-success btn-sm w-100">
              <i class="ti ti-check me-1"></i> Submit for Review
            </button>
          </form>
        @endif

        @if(($user->isSupervisor() || $user->isAdmin()) && $task->status === 'In Progress')
          <!-- Approve Modal Trigger -->
          <button class="btn btn-success btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
            <i class="ti ti-check me-1"></i> Approve
          </button>

          <!-- Reject Modal Trigger -->
          <button class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="ti ti-x me-1"></i> Reject
          </button>
        @endif

        @if($task->assignedTo?->id === $user->id && $task->status === 'In Progress' && $task->progress === 100)
          <form action="{{ route('tasks.mark-completed', $task) }}" method="POST" class="mb-2">
            @csrf
            <button type="submit" class="btn btn-success btn-sm w-100">
              <i class="ti ti-circle-check me-1"></i> Mark as Completed
            </button>
          </form>
        @endif

        <!-- Edit Button -->
        @if($task->created_by === $user->id && $task->status !== 'Completed')
          <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary btn-sm w-100">
            <i class="ti ti-edit me-1"></i> Edit
          </a>
        @endif
      </div>
    </div>

    <!-- Task Status Timeline -->
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Activity Timeline</h6>
      </div>
      <div class="card-body" style="max-height: 400px; overflow-y: auto;">
        <div class="timeline">
          <!-- Created -->
          <div class="timeline-item">
            <div class="timeline-indicator bg-primary"></div>
            <div class="timeline-content">
              <p class="mb-0"><small><strong>Created</strong></small></p>
              <small class="text-muted">{{ $task->created_at->format('d M Y H:i') }}</small>
              <small class="text-muted d-block">by {{ $task->creator->name }}</small>
            </div>
          </div>

          <!-- Activity from Comments (reversed untuk oldest first) -->
          @php
            $sortedComments = $task->comments->reverse();
          @endphp
          @forelse($sortedComments as $comment)
            <div class="timeline-item">
              <div class="timeline-indicator
                @if($comment->type === 'status_change') bg-info
                @elseif($comment->type === 'approval') bg-success
                @elseif($comment->type === 'rejection') bg-danger
                @else bg-secondary @endif
              "></div>
              <div class="timeline-content">
                <p class="mb-0"><small><strong>{{ ucfirst(str_replace('_', ' ', $comment->type)) }}</strong></small></p>
                <small class="text-muted">{{ $comment->created_at->format('d M Y H:i') }}</small>
                <small class="text-muted d-block">by {{ $comment->user->name }}</small>
                @if($comment->type !== 'status_change')
                  <small class="d-block mt-1">{{ $comment->comment }}</small>
                @endif
              </div>
            </div>
          @empty
            <div class="timeline-item">
              <div class="timeline-indicator bg-secondary"></div>
              <div class="timeline-content">
                <p class="mb-0"><small><strong>No activity yet</strong></small></p>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Approve Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('tasks.approve', $task) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Approval Comment *</label>
            <textarea class="form-control" name="comment" rows="4" placeholder="Please provide your feedback..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="ti ti-check me-1"></i> Approve
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('tasks.reject', $task) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Rejection Reason *</label>
            <textarea class="form-control" name="comment" rows="4" placeholder="Please explain why this task is rejected..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">
            <i class="ti ti-x me-1"></i> Reject
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-transparent border-0 shadow-none">
      <div class="modal-header border-0 p-0 justify-content-end">
        <button type="button" class="btn-close btn-close-white bg-dark p-2 rounded-circle" data-bs-dismiss="modal" aria-label="Close" style="margin-top: -40px;"></button>
      </div>
      <div class="modal-body p-0 text-center">
        <img id="previewImage" src="" class="img-fluid rounded shadow-lg" style="max-height: 85vh;">
        <h5 id="previewTitle" class="text-white mt-3 fw-bold"></h5>
      </div>
    </div>
  </div>
</div>

<style>
.timeline {
  position: relative;
  padding-left: 0;
}

.timeline-item {
  position: relative;
  padding-left: 40px;
  margin-bottom: 20px;
}

.timeline-indicator {
  position: absolute;
  left: 0;
  top: 0;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  border: 2px solid #fff;
}

.timeline-content {
  padding-left: 15px;
}

.timeline-item:not(:last-child)::before {
  content: '';
  position: absolute;
  left: 11px;
  top: 24px;
  width: 2px;
  height: 30px;
  background: #e9ecef;
}

.attachment-card:hover .image-overlay {
  opacity: 1 !important;
}

.attachment-card:hover img {
  transform: scale(1.1);
}

.transition-all {
  transition: all 0.3s ease-in-out;
}

.object-fit-cover {
  object-fit: cover;
}
</style>

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const imagePreviewModal = document.getElementById('imagePreviewModal');
  const previewImage = document.getElementById('previewImage');
  const previewTitle = document.getElementById('previewTitle');

  if (imagePreviewModal) {
    imagePreviewModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const src = button.getAttribute('data-img-src');
      const title = button.getAttribute('data-img-title');

      previewImage.src = src;
      previewTitle.textContent = title;
    });

    imagePreviewModal.addEventListener('hidden.bs.modal', function() {
      previewImage.src = '';
    });
  }
});
</script>
@endsection
@endsection
