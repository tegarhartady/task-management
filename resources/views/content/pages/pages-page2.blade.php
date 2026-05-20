@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Task Detail')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Back Link -->
  <div class="mb-3">
    <a href="{{ url('pages-tasks') }}" class="text-primary">
      <i class="ti ti-arrow-left me-1"></i> Back to Work Tasks
    </a>
  </div>

  <!-- Task Detail Card -->
  <div class="card mb-4">
    <div class="card-body">
      <!-- Badges -->
      <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="badge bg-primary">Supernata</span>
        <span class="badge bg-secondary">3D Modelling</span>
        <span class="badge bg-danger">HIGH</span>
        <span class="badge bg-label-warning">In Progress</span>
      </div>

      <!-- Title -->
      <h4 class="mb-4">Cicil 3D Assets</h4>

      <!-- Info Row -->
      <div class="row g-3">
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Assigned To</small>
          <div class="fw-medium">rangga</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Created By</small>
          <div class="fw-medium">rangga</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Due Date</small>
          <div class="fw-medium text-danger">2026-04-09</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Created</small>
          <div class="fw-medium">2026-04-09 04:23</div>
        </div>
        <div class="col-6 col-md-3">
          <small class="text-muted text-uppercase d-block mb-1">Started</small>
          <div class="fw-medium">2026-04-09 04:24</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Attachments Card -->
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center">
      <i class="ti ti-paperclip me-2"></i>
      <h5 class="card-title mb-0">Attachments (0)</h5>
    </div>
    <div class="card-body">
      <p class="text-muted mb-3">No attachments yet</p>
      <div class="row g-3">
        <div class="col-md-5">
          <label for="fileUpload" class="border border-dashed rounded p-4 text-center d-block" style="border-style: dashed !important; cursor: pointer;">
            <i class="ti ti-cloud-upload fs-3 text-muted mb-2 d-block"></i>
            <span class="text-muted">Upload file</span>
          </label>
          <input type="file" class="form-control d-none" id="fileUpload">
        </div>
        <div class="col-md-7">
          <div class="row g-2">
            <div class="col-12">
              <input type="text" class="form-control" placeholder="Paste a URL...">
            </div>
            <div class="col-8">
              <input type="text" class="form-control" placeholder="Label (optional)">
            </div>
            <div class="col-4">
              <button class="btn btn-primary w-100">
                <i class="ti ti-link me-1"></i> Add Link
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Time Tracking Card -->
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center">
      <i class="ti ti-clock me-2"></i>
      <h5 class="card-title mb-0">Time Tracking</h5>
      <span class="ms-2 text-muted">Total: 0.0 hours</span>
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <i class="ti ti-clock me-1 text-muted"></i>
          <span>11:24:15 → ...</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Activity Card -->
  <div class="card mb-4">
    <div class="card-header d-flex align-items-center">
      <i class="ti ti-message-dots me-2"></i>
      <h5 class="card-title mb-0">Activity</h5>
    </div>
    <div class="card-body">
      <!-- Activity Items -->
      <div class="mb-3">
        <div class="d-flex align-items-start border-start border-primary border-3 ps-3 mb-3" style="background-color: rgba(115,103,240,0.08); padding: 12px; border-radius: 0 6px 6px 0;">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
              <i class="ti ti-user-plus me-2 text-primary"></i>
              <span class="fw-medium">rangga</span>
            </div>
            <div class="text-muted">Assigned to rangga</div>
          </div>
          <small class="text-muted">2026-04-09 04:23</small>
        </div>

        <div class="d-flex align-items-start border-start border-success border-3 ps-3" style="background-color: rgba(40,199,111,0.08); padding: 12px; border-radius: 0 6px 6px 0;">
          <div class="flex-grow-1">
            <div class="d-flex align-items-center mb-1">
              <i class="ti ti-refresh me-2 text-success"></i>
              <span class="fw-medium">rangga</span>
            </div>
            <div class="text-muted">Started working on this task</div>
          </div>
          <small class="text-muted">2026-04-09 04:24</small>
        </div>
      </div>

      <!-- Add Comment -->
      <div class="d-flex gap-2">
        <input type="text" class="form-control" placeholder="Add a comment...">
        <button class="btn btn-primary">Send</button>
      </div>
    </div>
  </div>

  <!-- Sticky Footer Action Bar -->
  <div class="card position-sticky bottom-0" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f9ff 100%);">
    <div class="card-body py-3">
      <div class="d-flex justify-content-center align-items-center gap-3">
        <button class="btn btn-warning rounded-pill px-4">
          <i class="ti ti-player-stop me-1"></i> Clock Out
        </button>
        <span class="text-success fw-medium">4h 12m 4s</span>
        <button class="btn btn-success rounded-pill px-4">
          <i class="ti ti-send me-1"></i> Submit for Review
        </button>
      </div>
    </div>
  </div>
</div>
@endsection
