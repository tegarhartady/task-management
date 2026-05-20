{{-- filepath: resources/views/content/pages/pages-content.blade.php --}}
@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Content Briefs')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Content Briefs</h4>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-primary"><i class="ti ti-robot me-2"></i>AI Brief</button>
        <a href="{{ route('pages-content.create') }}" class="btn btn-primary">
          <i class="ti ti-plus me-2"></i>New Brief
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-2 mb-2">
      <div class="card text-center">
        <div class="card-body">
          <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
          <small class="text-muted">Total</small>
        </div>
      </div>
    </div>
    <div class="col-md-2 mb-2">
      <div class="card text-center">
        <div class="card-body">
          <h3 class="mb-0 text-warning">{{ $stats['draft'] ?? 0 }}</h3>
          <small class="text-muted">Draft</small>
        </div>
      </div>
    </div>
    <div class="col-md-2 mb-2">
      <div class="card text-center">
        <div class="card-body">
          <h3 class="mb-0 text-danger">{{ $stats['taken'] ?? 0 }}</h3>
          <small class="text-muted">Taken</small>
        </div>
      </div>
    </div>
    <div class="col-md-2 mb-2">
      <div class="card text-center">
        <div class="card-body">
          <h3 class="mb-0 text-primary">{{ $stats['submitted'] ?? 0 }}</h3>
          <small class="text-muted">Submitted</small>
        </div>
      </div>
    </div>
    <div class="col-md-2 mb-2">
      <div class="card text-center">
        <div class="card-body">
          <h3 class="mb-0 text-info">{{ $stats['revision'] ?? 0 }}</h3>
          <small class="text-muted">Revision</small>
        </div>
      </div>
    </div>
    <div class="col-md-2 mb-2">
      <div class="card text-center">
        <div class="card-body">
          <h3 class="mb-0 text-success">{{ $stats['approved'] ?? 0 }}</h3>
          <small class="text-muted">Approved</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('pages-content') }}" class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="All">All Status</option>
            <option value="DRAFT" {{ $filters['status'] == 'DRAFT' ? 'selected' : '' }}>Draft</option>
            <option value="TAKEN" {{ $filters['status'] == 'TAKEN' ? 'selected' : '' }}>Taken</option>
            <option value="SUBMITTED" {{ $filters['status'] == 'SUBMITTED' ? 'selected' : '' }}>Submitted</option>
            <option value="REVISION" {{ $filters['status'] == 'REVISION' ? 'selected' : '' }}>Revision</option>
            <option value="APPROVED" {{ $filters['status'] == 'APPROVED' ? 'selected' : '' }}>Approved</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Brand</label>
          <select name="brand" class="form-select" onchange="this.form.submit()">
            <option value="All">All Brands</option>
            <option value="SUPERNATA" {{ $filters['brand'] == 'SUPERNATA' ? 'selected' : '' }}>Supernata</option>
            <option value="DEKORNATA" {{ $filters['brand'] == 'DEKORNATA' ? 'selected' : '' }}>Dekornata</option>
            <option value="CRAFTNATA" {{ $filters['brand'] == 'CRAFTNATA' ? 'selected' : '' }}>Craftnata</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Type</label>
          <select name="type" class="form-select" onchange="this.form.submit()">
            <option value="All">All Types</option>
            <option value="REEL" {{ $filters['type'] == 'REEL' ? 'selected' : '' }}>REEL</option>
            <option value="IGS" {{ $filters['type'] == 'IGS' ? 'selected' : '' }}>IGS</option>
            <option value="POST" {{ $filters['type'] == 'POST' ? 'selected' : '' }}>POST</option>
            <option value="CAROUSEL" {{ $filters['type'] == 'CAROUSEL' ? 'selected' : '' }}>CAROUSEL</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Search</label>
          <input type="text" name="search" class="form-control" value="{{ $filters['search'] }}"
            placeholder="Search briefs..." onkeydown="if(event.key==='Enter'){this.form.submit();}">
        </div>
      </form>
    </div>
  </div>

  <!-- Brief List -->
  @if($briefs->count() > 0)
    @foreach($briefs as $brief)
      @php
        $statusColors = [
          'DRAFT' => '#ff9f43',
          'TAKEN' => '#ea5455',
          'SUBMITTED' => '#7367f0',
          'REVISION' => '#00cfe8',
          'APPROVED' => '#28c76f',
        ];
        $borderColor = $statusColors[$brief->status] ?? '#ccc';
      @endphp
      <a href="{{ route('pages-content.show', $brief->id) }}" class="text-decoration-none">
        <div class="card mb-3" style="border-left: 4px solid {{ $borderColor }}; cursor: pointer; transition: all 0.3s ease;">
          <div class="card-body" style="display: flex; justify-content: space-between; align-items: start;">
            <div style="flex: 1;">
              <!-- Badges -->
              <div class="mb-2">
                <span class="badge bg-secondary me-2">{{ $brief->brand }}</span>
                <span class="badge bg-dark me-2">{{ $brief->type }}</span>
                <span class="badge
                  @if($brief->status=='DRAFT') bg-warning text-dark
                  @elseif($brief->status=='TAKEN') bg-danger
                  @elseif($brief->status=='SUBMITTED') bg-primary
                  @elseif($brief->status=='REVISION') bg-info
                  @elseif($brief->status=='APPROVED') bg-success
                  @else bg-secondary @endif
                ">{{ $brief->status }}</span>
                @if($brief->is_ai)
                  <span class="badge bg-dark"><i class="ti ti-robot"></i> AI</span>
                @endif
              </div>

              <!-- Title -->
              <h6 class="mb-2">{{ $brief->title }}</h6>

              <!-- Description (truncated) -->
              <p class="text-muted small mb-2">{{ Str::limit(strip_tags($brief->concept ?? ''), 100, '...') }}</p>

              <!-- Meta -->
              <small class="text-muted">
                By {{ $brief->creator->name }} • {{ $brief->created_at->format('d M Y') }}
                @if($brief->due_date)
                  • <span class="{{ $brief->due_date->isPast() ? 'text-danger fw-bold' : '' }}">
                    <i class="ti ti-calendar-event me-1"></i>Due: {{ $brief->due_date->format('d M Y') }}
                  </span>
                @endif
              </small>

              <!-- Assignees -->
              @if($brief->assignees->count() > 0)
                <div class="mt-2">
                  <small class="text-muted d-block mb-1">Assigned to:</small>
                  @foreach($brief->assignees as $assignee)
                    <span class="badge bg-label-info rounded-pill me-1" title="{{ $assignee->email }}" style="font-size: 0.7rem;">
                      <i class="ti ti-user me-1"></i>{{ $assignee->name }}
                    </span>
                  @endforeach
                </div>
              @endif
            </div>

            <!-- Right side info -->
            <div class="text-end ms-3" style="min-width: 150px;">
              @if($brief->comments > 0)
                <div class="mb-2">
                  <i class="ti ti-message me-1"></i>
                  <strong>{{ $brief->comments }}</strong> comment
                </div>
              @else
                <div class="mb-2 text-muted">
                  <i class="ti ti-message-off me-1"></i>
                  <small>No comments</small>
                </div>
              @endif
            </div>
          </div>
        </div>
      </a>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
      <div class="text-muted">
        Showing <strong>{{ ($briefs->currentPage() - 1) * $briefs->perPage() + 1 }}-{{ min($briefs->currentPage() * $briefs->perPage(), $briefs->total()) }}</strong> of <strong>{{ $briefs->total() }}</strong> briefs
      </div>
      <div>
        {{ $briefs->links() }}
      </div>
    </div>
  @else
    <div class="text-center py-5">
      <i class="ti ti-inbox" style="font-size: 3rem; color: #ccc;"></i>
      <p class="text-muted mt-3">No briefs found</p>
    </div>
  @endif
</div>

<style>
  .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
</style>

@endsection
