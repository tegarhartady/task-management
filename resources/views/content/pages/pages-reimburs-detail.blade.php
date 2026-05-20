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
  <div class="mb-4">
    <a href="{{ route('pages-reimburs') }}" class="text-primary text-decoration-none">
      <i class="ti ti-arrow-left me-2"></i>Back to Reimbursement
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Main Content Card -->
  <div class="card mb-4">
    <div class="card-body">
      <!-- Badges Row -->
      <div class="mb-3">
        <span class="badge bg-secondary me-2">{{ $reimburs->category }}</span>
        <span class="badge 
          @if($reimburs->status=='Approved') bg-success
          @elseif($reimburs->status=='Pending') bg-warning text-dark
          @else bg-danger @endif
        ">{{ $reimburs->status }}</span>
      </div>

      <!-- Title -->
      <h3 class="mb-2">{{ $reimburs->title }}</h3>

      <!-- Metadata -->
      <p class="text-muted mb-4">
        <span class="me-3"><i class="ti ti-user me-1"></i>Submitted by {{ $reimburs->submittedBy->name }}</span>
        <span><i class="ti ti-calendar me-1"></i>{{ $reimburs->created_at->format('d M Y') }}</span>
      </p>

      <!-- Status Section -->
      <div class="mb-4">
        <div class="card border-0 bg-light">
          <div class="card-body">
            <h6 class="mb-3">Status</h6>
            @if($isPending)
              <div class="d-flex align-items-center">
                <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <span class="text-warning">Waiting for approval</span>
              </div>
            @elseif($reimburs->status === 'Approved')
              <div class="d-flex align-items-center">
                <i class="ti ti-check text-success me-2" style="font-size: 1.2rem;"></i>
                <div>
                  <span class="text-success">Approved by {{ $reimburs->approvedBy->name }}</span><br>
                  <small class="text-muted">{{ $reimburs->approved_at->format('d M Y H:i') }}</small>
                </div>
              </div>
            @else
              <div class="d-flex align-items-center">
                <i class="ti ti-x text-danger me-2" style="font-size: 1.2rem;"></i>
                <span class="text-danger">Rejected</span>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Details Section -->
      <div class="mb-4">
        <h6 class="mb-3">Details</h6>
        
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <small class="text-muted d-block mb-2 text-uppercase">Category</small>
                <span class="badge bg-secondary">{{ $reimburs->category }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <small class="text-muted d-block mb-2 text-uppercase">Amount</small>
                <h5 class="mb-0 text-primary">Rp{{ number_format($reimburs->amount, 0, ',', '.') }}</h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <small class="text-muted d-block mb-2 text-uppercase">Date</small>
                <strong>{{ $reimburs->date->format('d M Y') }}</strong>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card border-0 bg-light">
              <div class="card-body">
                <small class="text-muted d-block mb-2 text-uppercase">Submitted By</small>
                <strong>{{ $reimburs->submittedBy->name }}</strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Description Section -->
      <div class="mb-4">
        <h6 class="mb-3">Description</h6>
        <div class="card border-0 bg-light">
          <div class="card-body">
            <p class="mb-0">{{ $reimburs->description ?? 'No description provided.' }}</p>
          </div>
        </div>
      </div>

      <!-- Proof of Expense -->
      @if($reimburs->attachments->count() > 0)
        <div class="mb-4">
          <h6 class="mb-3">Proof of Expense</h6>
          <div class="row">
            @foreach($reimburs->attachments as $attachment)
              <div class="col-md-3 mb-3">
                <div class="card overflow-hidden h-100">
                  @if($attachment->file_type === 'image')
                    <div style="height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                      <img src="{{ Storage::url($attachment->file_path) }}" 
                        class="img-fluid" 
                        style="height: 100%; width: 100%; object-fit: cover; cursor: pointer;"
                        data-bs-toggle="modal" 
                        data-bs-target="#imageModal{{ $attachment->id }}">
                    </div>
                  @elseif($attachment->file_type === 'pdf')
                    <div style="height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                      <i class="ti ti-file-pdf" style="font-size: 3rem; color: #dc3545;"></i>
                      <small class="text-muted mt-2">PDF Document</small>
                    </div>
                  @else
                    <div style="height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                      <i class="ti ti-file" style="font-size: 3rem; color: #6c757d;"></i>
                      <small class="text-muted mt-2">Document</small>
                    </div>
                  @endif
                  <div class="card-body pt-2 pb-2">
                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" 
                      class="text-primary text-decoration-none small d-block text-truncate" 
                      title="{{ $attachment->original_name }}">
                      {{ Str::limit($attachment->original_name, 25) }}
                    </a>
                  </div>
                </div>
              </div>

              <!-- Image Modal -->
              @if($attachment->file_type === 'image')
                <div class="modal fade" id="imageModal{{ $attachment->id }}" tabindex="-1">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">{{ $attachment->original_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body text-center">
                        <img src="{{ Storage::url($attachment->file_path) }}" class="img-fluid" alt="{{ $attachment->original_name }}">
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        </div>
      @endif

      <!-- Rejection Reason -->
      @if($reimburs->status === 'Rejected' && !empty($reimburs->rejection_reason))
        <div class="mb-4">
          <div class="alert alert-danger">
            <h6 class="alert-heading mb-2">Rejection Reason</h6>
            <p class="mb-0">{{ $reimburs->rejection_reason }}</p>
          </div>
        </div>
      @endif
    </div>
  </div>

  <!-- Action Buttons (Supervisor) -->
  @if($isSupervisor && $isPending)
    <div class="card position-sticky bottom-0" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f9ff 100%); box-shadow: 0 -2px 8px rgba(0,0,0,0.1);">
      <div class="card-body py-4">
        <div class="d-flex justify-content-center gap-3">
          <form action="{{ route('reimburs.approve', $reimburs->id) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-success px-4 rounded-pill">
              <i class="ti ti-check me-2"></i>Approve
            </button>
          </form>
          <button type="button" class="btn btn-danger px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="ti ti-x me-2"></i>Reject
          </button>
        </div>
      </div>
    </div>
  @endif
</div>

<!-- Reject Modal -->
@if($isSupervisor && $isPending)
  <div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reject Reimbursement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('reimburs.reject', $reimburs->id) }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
              <textarea class="form-control" name="reason" rows="4" 
                placeholder="Please provide the reason for rejection..." required></textarea>
              @error('reason')
                <small class="text-danger">{{ $message }}</small>
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
