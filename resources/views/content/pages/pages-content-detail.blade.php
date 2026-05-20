{{-- filepath: resources/views/content/pages/pages-content-detail.blade.php --}}
@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', $brief->title ?? 'Brief Detail')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Back Link -->
  <div class="mb-4">
    <a href="{{ url('pages-content') }}" class="text-decoration-none text-primary">
      <i class="ti ti-arrow-left me-2"></i>Back to Briefs
    </a>
  </div>

  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-12">
      <!-- Badges -->
      <div class="mb-3 d-flex flex-wrap gap-2">
        <span class="badge bg-primary">{{ $brief->brand ?? 'N/A' }}</span>
        <span class="badge bg-dark">{{ $brief->type ?? 'N/A' }}</span>
        <span class="badge bg-warning text-dark">{{ $brief->status ?? 'N/A' }}</span>
        @if($brief->is_ai)
          <span class="badge bg-dark"><i class="ti ti-robot me-1"></i>AI</span>
        @endif
      </div>

      <!-- Title -->
      <h3 class="mb-2">{{ $brief->title ?? 'No Title' }}</h3>

      <!-- Meta Info -->
      <div class="d-flex align-items-center flex-wrap gap-3">
        <small class="text-muted">
          Created by <strong>{{ $brief->creator->name ?? 'Unknown' }}</strong> • {{ $brief->created_at->format('d M Y, H:i') }}
        </small>
        @if($brief->due_date)
          <span class="badge {{ $brief->due_date->isPast() ? 'bg-label-danger' : 'bg-label-info' }}">
            <i class="ti ti-calendar-event me-1"></i>Due Date: {{ $brief->due_date->format('d M Y') }}
          </span>
        @endif
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <!-- Working on this Section -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <i class="ti ti-briefcase me-2" style="font-size: 1.2rem;"></i>
            <h6 class="mb-0">Working on this ({{ $brief->assignees->count() }})</h6>
          </div>
          @if($brief->assignees->count() == 0)
            <p class="text-muted mb-0">No one assigned yet.</p>
          @else
            <div class="row g-2">
              @foreach($brief->assignees as $assignee)
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                  <div class="border rounded p-3 text-center h-100">
                    <div class="avatar avatar-md mx-auto mb-2">
                      <div class="avatar-initial bg-label-info rounded-circle">
                        {{ substr($assignee->name, 0, 1) }}
                      </div>
                    </div>
                    <p class="mb-0 mt-2 text-truncate" title="{{ $assignee->name }}">
                      <small>{{ $assignee->name }}</small>
                    </p>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      <!-- The Big Picture Section -->
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center mb-4">
            <i class="ti ti-photo me-2" style="font-size: 1.2rem;"></i>
            <h6 class="mb-0">The Big Picture</h6>
          </div>

          <!-- Hook Section -->
          @if(!empty($brief->hook))
            <div class="mb-4">
              <div class="border-start border-warning ps-3 py-2" style="background-color: #fff8e1;">
                <small class="text-warning fw-600 text-uppercase d-block mb-2">Hook (First 1-3 Seconds)</small>
                <p class="mb-0 text-dark">{{ $brief->hook }}</p>
              </div>
            </div>
          @endif

          <!-- Concept Section -->
          @if(!empty($brief->concept))
            <div class="mb-4">
              <div class="border-start border-primary ps-3 py-2" style="background-color: #f0f7ff;">
                <small class="text-primary fw-600 text-uppercase d-block mb-2">Concept / Story Arc</small>
                <p class="mb-0 text-dark" style="white-space: pre-wrap; line-height: 1.6;">{!! $brief->concept !!}</p>
              </div>
            </div>
          @endif

          <!-- Visual Direction Section -->
          @if(!empty($brief->visual_direction))
            <div class="mb-4">
              <div class="border-start border-info ps-3 py-2" style="background-color: #f0fbff;">
                <small class="text-info fw-600 text-uppercase d-block mb-2">Visual Direction</small>
                <p class="mb-0 text-dark" style="white-space: pre-wrap; line-height: 1.6;">{!! $brief->visual_direction !!}</p>
              </div>
            </div>
          @endif

          <!-- Voiceover Script Section -->
          @if(!empty($brief->voiceover))
            <div class="mb-0">
              <div class="border-start border-success ps-3 py-2" style="background-color: #f0fdf4;">
                <small class="text-success fw-600 text-uppercase d-block mb-2">Voiceover Script</small>
                <p class="mb-0 text-dark" style="white-space: pre-wrap; line-height: 1.6;">{!! $brief->voiceover !!}</p>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- Attachments Section -->
      @if($brief->attachments->count() > 0)
        <div class="card mt-4">
          <div class="card-body">
            <h6 class="mb-4">
              <i class="ti ti-file-image me-2"></i>Reference Images & Documents
            </h6>

            <div class="row g-3">
              @foreach($brief->attachments as $attachment)
                <div class="col-6 col-md-4 col-lg-3">
                  @if($attachment->file_type === 'image')
                    <div class="position-relative">
                      <img src="{{ Storage::url($attachment->file_path) }}" 
                        class="img-fluid rounded" 
                        style="object-fit: cover; height: 200px; width: 100%; cursor: pointer;"
                        data-bs-toggle="modal" 
                        data-bs-target="#imageModal{{ $attachment->id }}">
                      <small class="d-block mt-2 text-truncate" title="{{ $attachment->original_name }}">
                        {{ $attachment->original_name }}
                      </small>
                    </div>

                    <!-- Image Modal -->
                    <div class="modal fade" id="imageModal{{ $attachment->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">{{ $attachment->original_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-center">
                            <img src="{{ Storage::url($attachment->file_path) }}" class="img-fluid" style="max-height: 80vh;">
                          </div>
                        </div>
                      </div>
                    </div>
                  @elseif($attachment->file_type === 'pdf')
                    <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                      <i class="ti ti-file-pdf" style="font-size: 3rem; color: #dc3545;"></i>
                      <small class="mt-2 text-truncate" title="{{ $attachment->original_name }}">
                        {{ $attachment->original_name }}
                      </small>
                      <a href="{{ Storage::url($attachment->file_path) }}" class="btn btn-sm btn-danger mt-2" download>
                        <i class="ti ti-download me-1"></i>Download
                      </a>
                    </div>
                  @else
                    <div class="border rounded p-3 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                      <i class="ti ti-file" style="font-size: 3rem; color: #7367f0;"></i>
                      <small class="mt-2 text-truncate" title="{{ $attachment->original_name }}">
                        {{ $attachment->original_name }}
                      </small>
                      <a href="{{ Storage::url($attachment->file_path) }}" class="btn btn-sm btn-primary mt-2" download>
                        <i class="ti ti-download me-1"></i>Download
                      </a>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      <!-- Comments Section -->
      <div class="card mt-4">
        <div class="card-header">
          <h6 class="mb-0">
            <i class="ti ti-message-circle me-2"></i>Comments ({{ $brief->briefComments->count() }})
          </h6>
        </div>
        <div class="card-body">
          <!-- Comments List -->
          <div id="commentsList" class="mb-4" style="max-height: 400px; overflow-y: auto;">
            @if($brief->briefComments->count() > 0)
              @foreach($brief->briefComments as $comment)
                <div class="d-flex gap-2 mb-3 pb-3 border-bottom">
                  <div class="flex-shrink-0">
                    <div class="avatar avatar-sm">
                      <div class="avatar-initial bg-primary rounded-circle">
                        {{ substr($comment->user->name, 0, 1) }}
                      </div>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1">{{ $comment->user->name }}</h6>
                        <p class="mb-2 text-dark">{{ $comment->comment }}</p>
                      </div>
                      <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                  </div>
                </div>
              @endforeach
            @else
              <p class="text-muted mb-0">No comments yet. Be the first to comment!</p>
            @endif
          </div>

          <!-- Add Comment Form -->
          <form id="commentForm">
            @csrf
            <input type="hidden" name="brief_id" value="{{ $brief->id }}">
            <div class="input-group">
              <input type="text" class="form-control" id="commentText" name="comment" placeholder="Add a comment..." required>
              <button class="btn btn-primary" type="submit">
                <i class="ti ti-send me-1"></i>Send
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('commentForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('{{ route("pages-content.comment") }}', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    }
  })
  .then(response => {
    if (response.ok) {
      return response.json();
    }
    throw new Error('Failed to add comment');
  })
  .then(data => {
    if (data.success) {
      // Clear input
      document.getElementById('commentText').value = '';
      
      // Reload page to show new comment
      location.reload();
    }
  })
  .catch(error => {
    alert('Error: ' + error.message);
  });
});
</script>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.6rem;
  }

  @media (max-width: 576px) {
    h3 {
      font-size: 1.5rem;
    }

    .card-body {
      padding: 1rem;
    }
  }
</style>

@endsection
