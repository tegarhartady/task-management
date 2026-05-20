@php
$configData = Helper::appClasses();

// Status colors like pages-content
$statusColors = [
  'pending' => '#ff9f43',
  'approved' => '#28c76f',
  'rejected' => '#ea5455',
];

// Count totals
$allReimbursements = \App\Models\Reimbursement::all();
$countPending = $allReimbursements->where('status', 'Pending')->count();
$countApproved = $allReimbursements->where('status', 'Approved')->count();
$countRejected = $allReimbursements->where('status', 'Rejected')->count();
$totalAmount = $allReimbursements->sum('amount');
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Reimbursement')

@section('content')
<div class="row">
  <div class="col-12 d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Reimbursement</h4>
    <a href="{{ route('reimburs.create') }}" class="btn btn-primary">+ Create Reimbursement</a>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- Status Count Cards -->
<div class="row mb-3">
  <div class="col-6 col-md-2 mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0">{{ count($reimbursements) }}</h3>
        <small class="text-muted">Total</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-2 mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-warning">{{ $countPending }}</h3>
        <small class="text-muted">Pending</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-2 mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-success">{{ $countApproved }}</h3>
        <small class="text-muted">Approved</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-2 mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-danger">{{ $countRejected }}</h3>
        <small class="text-muted">Rejected</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-primary">Rp{{ number_format($totalAmount, 0, ',', '.') }}</h3>
        <small class="text-muted">Total Amount</small>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('pages-reimburs') }}" class="mb-3">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">
          <select class="form-select w-auto" name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
          <select class="form-select w-auto" name="category" onchange="this.form.submit()">
            <option value="">All Category</option>
            <option value="Transport" {{ request('category') == 'Transport' ? 'selected' : '' }}>Transport</option>
            <option value="Meal" {{ request('category') == 'Meal' ? 'selected' : '' }}>Meal</option>
            <option value="Office Supplies" {{ request('category') == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
            <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
          </select>
          <div class="flex-grow-1">
            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search by title..." onkeydown="if(event.key==='Enter'){this.form.submit();}">
          </div>
          <button type="submit" class="btn btn-outline-secondary">Filter</button>
          @if(request('status') || request('category') || request('search'))
            <a href="{{ route('pages-reimburs') }}" class="btn btn-link">Reset</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Reimbursement List -->
<div class="row">
  <div class="col-12">
    @if($reimbursements->count() > 0)
      @foreach($reimbursements as $item)
        @php
          $borderColor = $statusColors[strtolower($item->status)] ?? '#ccc';
        @endphp
        <a href="{{ route('reimburs.detail', $item->id) }}" class="text-decoration-none">
          <div class="card mb-3" style="border-left:4px solid {{ $borderColor }}; cursor:pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
            <div class="card-body d-flex justify-content-between align-items-start">
              <div style="max-width:78%;">
                <h6 class="mb-1 text-body">{{ $item->title }}</h6>
                <p class="text-muted mb-2">{{ Str::limit($item->description, 80) }}</p>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge 
                    @if($item->status=='Approved') bg-success
                    @elseif($item->status=='Pending') bg-warning text-dark
                    @else bg-danger @endif
                  ">{{ $item->status }}</span>
                  <span class="badge bg-secondary">{{ $item->category }}</span>
                  <span class="badge bg-light text-dark">Rp{{ number_format($item->amount, 0, ',', '.') }}</span>
                </div>
              </div>
              <div class="text-end text-muted" style="min-width:140px;">
                <div class="mb-2"><i class="ti ti-user me-1"></i> {{ $item->submittedBy->name }}</div>
                <div><i class="ti ti-calendar me-1"></i> {{ $item->date->format('d M Y') }}</div>
              </div>
            </div>
          </div>
        </a>
      @endforeach

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
          Showing <strong>{{ ($reimbursements->currentPage() - 1) * $reimbursements->perPage() + 1 }}-{{ min($reimbursements->currentPage() * $reimbursements->perPage(), $reimbursements->total()) }}</strong> of <strong>{{ $reimbursements->total() }}</strong> requests
        </div>
        <div>
          {{ $reimbursements->links() }}
        </div>
      </div>
    @else
      <div class="card">
        <div class="card-body text-center py-5 text-muted">
          <i class="ti ti-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="mt-3 mb-0">No reimbursement requests found</p>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
