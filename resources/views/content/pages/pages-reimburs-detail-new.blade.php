@php
$configData = Helper::appClasses();
$user = auth()->user();
$isSupervisor = $user && ($user->isSupervisor() || $user->isAdmin());
$isPending = $reimburs->status === 'Pending';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Reimbursement Detail')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Back Link -->
  <div class="mb-3">
    <a href="{{ route('pages-reimburs') }}" class="text-primary">
      <i class="ti ti-arrow-left me-1"></i> Back to Reimbursement
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Reimbursement Detail Card -->
  <div class="card mb-4">
    <div class="card-body">
      <!-- Badges -->
      <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="badge bg-secondary">{{ $reimburs->category }}</span>
        <span class="badge 
          @if($reimburs->status=='Approved') bg-success
          @elseif($reimburs->status=='Pending') bg-warning text-dark
          @else bg-danger @endif
        ">{{ strtoupper($reimburs->status) }}</span>
        <span class="badge bg-primary">Rp{{ number_format($reimburs->amount, 0, ',', '.') }}</span>
      </div>

      <!-- Title -->
      <h4 class="mb-4">{{ $reimburs->title }}</h4>

      <!-- Info Row -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Submitted By</small>
          <div class="fw-medium">{{ $reimburs->submittedBy->name }}</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Date</small>
          <div class="fw-medium">{{ $reimburs->date->format('d M Y') }}</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Submitted At</small>
          <div class="fw-medium">{{ $reimburs->created_at->format('d M Y H:i') }}</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Attachments</small>
          <div class="fw-medium">{{ $reimburs->attachments->count() }} file(s)</div>
        </div>
      </div>

      <!-- Description -->
      <div class="mb-4">
        <strong>Description:</strong>
        <div class="mt-2 p-3 bg-light rounded">
          <p class="mb-0">{{ $reimburs->description ?? 'No description provided.' }}</p>
        </div>
      </div>

      <!-- Proof of Expense / Attachments -->
      @if($reimburs->attachments->count() > 0)
        <div class="mb-4">
          <strong class="d-block mb-3">Proof of Expense:</strong>
          <div class="row">
            @foreach($reimburs->attachments as $attachment)
              <div class="col-md-3 mb-3">
                <div class="card h-100">
                  @if($attachment->file_type === 'image')
                    <div style="height: 180px; overflow: hidden; background: #f5f5f5;">
                      <img src="{{ Storage::url($attachment->file_path) }}" alt="{{ $attachment->original_name }}" 
                        class="img-fluid" style="height: 100%; width: 100%; object-fit: cover;">
                    </div>
                  @elseif($attachment->file_type === 'pdf')
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 180px; background: #f5f5f5;">
                      <div class="text-center">
                        <i class="ti ti-file-pdf" style="font-size: 2.5rem; color: #dc3545;"></i>
                        <p class="mb-0 small mt-2">PDF File</p>
                      </div>
                    </div>
                  @else
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 180px; background: #f5f5f5;">
                      <div class="text-center">
                        <i class="ti ti-file" style="font-size: 2.5rem; color: #6c757d;"></i>
                        <p class="mb-0 small mt-2">Document</p>
                      </div>
                    </div>
                  @endif
                  <div class="card-body pt-2">
                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-primary small d-block text-truncate" title="{{ $attachment->original_name }}">
                      {{ $attachment->original_name }}
                    </a>
                    <small class="text-muted">{{ $attachment->file_type }}</small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Rejection Reason (if rejected) -->
      @if(!empty($reimburs->rejection_reason))
        <div class="alert alert-danger">
          <strong>Rejection Reason:</strong><br>
          {{ $reimburs->rejection_reason }}
        </div>
      @endif

      <!-- Approved Info (if approved) -->
      @if($reimburs->approved_by)
        <div class="alert alert-success">
          <strong>Approved by:</strong> {{ $reimburs->approvedBy->name }}<br>
          <strong>Approved at:</strong> {{ $reimburs->approved_at->format('d M Y H:i') }}
        </div>
      @endif
    </div>
  </div>

  <!-- Action Bar (Supervisor) -->
  @if($isSupervisor && $isPending)
    <div class="card position-sticky bottom-0" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f9ff 100%);">
      <div class="card-body py-3">
        <div class="d-flex justify-content-center align-items-center gap-3">
          <form action="{{ route('reimburs.approve', $reimburs->id) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-success rounded-pill px-4">
              <i class="ti ti-check me-1"></i> Approve
            </button>
          </form>
          <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="ti ti-x me-1"></i> Reject
          </button>
        </div>
      </div>
    </div>
  @endif
</div>

<!-- Reject Modal -->
@if($isSupervisor && $isPending)
  <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reject Reimbursement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('reimburs.reject', $reimburs->id) }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
              <textarea class="form-control" name="reason" rows="4" placeholder="Please provide the reason for rejection..." required></textarea>
              @error('reason')
                <span class="text-danger small">{{ $message }}</span>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Reject</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
@endsection
