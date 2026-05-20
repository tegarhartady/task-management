@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Karyawan Dashboard')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h4>
        <p class="text-muted mb-0">Dashboard tugas dan aktivitas pribadi Anda</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">
          <i class="ti ti-download me-1"></i> Export Laporan
        </button>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1 text-muted small">TUGAS AKTIF</p>
          <h3 class="mb-0" style="color: #667eea;">{{ $inProgressTasks }}</h3>
        </div>
        <span class="badge bg-primary p-2">
          <i class="ti ti-list-check" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100 border-start border-danger border-4">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1 text-danger small">TERLAMBAT</p>
          <h3 class="mb-0 text-danger fw-bold">{{ $overdueTasks }}</h3>
        </div>
        <span class="badge bg-label-danger p-2">
          <i class="ti ti-alert-triangle" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1 text-muted small">BRIEF TERSEDIA</p>
          <h3 class="mb-0" style="color: #0dcaf0;">{{ $totalBriefs }}</h3>
        </div>
        <span class="badge bg-info p-2">
          <i class="ti ti-book" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1 text-muted small">REIMBURS APPROVED</p>
          <h3 class="mb-0" style="color: #20c997;">Rp{{ number_format($approvedReimburse, 0, ',', '.') }}</h3>
        </div>
        <span class="badge bg-success p-2">
          <i class="ti ti-receipt" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <!-- My Tasks -->
  <div class="col-lg-8 col-12 mb-4">
    <div class="card shadow-sm h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="ti ti-layout-list me-2 text-primary"></i>Tugas Terbaru</h5>
        <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Judul</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Deadline</th>
              </tr>
            </thead>
            <tbody>
              @forelse($myTasks as $task)
                <tr>
                  <td>
                    <h6 class="mb-0 fw-semibold">{{ $task->title }}</h6>
                  </td>
                  <td>
                    <span class="badge 
                      @if($task->priority === 'High') bg-danger
                      @elseif($task->priority === 'Medium') bg-warning
                      @else bg-info @endif
                    ">{{ $task->priority }}</span>
                  </td>
                  <td>
                    <span class="badge 
                      @if($task->status === 'Completed') bg-success
                      @elseif($task->status === 'In Progress') bg-warning
                      @elseif($task->status === 'Pending Review') bg-primary
                      @else bg-secondary @endif
                    ">{{ $task->status }}</span>
                  </td>
                  <td>
                    <small class="{{ $task->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                      {{ $task->due_date?->format('d M Y') ?? 'N/A' }}
                    </small>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">Tidak ada tugas yang diberikan kepada Anda</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="col-lg-4 col-12 mb-4">
    <div class="card shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0 fw-bold"><i class="ti ti-chart-bar me-2 text-success"></i>Ringkasan</h5>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <div class="d-flex justify-content-between mb-1">
            <small class="fw-medium">Penyelesaian Tugas</small>
            @php
              $total = $completedTasks + $inProgressTasks;
              $rate = $total > 0 ? round(($completedTasks / $total) * 100) : 0;
            @endphp
            <small class="fw-bold">{{ $rate }}%</small>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $rate }}%" aria-valuenow="{{ $rate }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        
        <div class="list-group list-group-flush mb-3">
          <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
            <span class="text-muted"><i class="ti ti-circle-filled text-primary me-2" style="font-size: 8px;"></i>Selesai</span>
            <span class="fw-bold text-success">{{ $completedTasks }}</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
            <span class="text-muted"><i class="ti ti-circle-filled text-warning me-2" style="font-size: 8px;"></i>Dalam Proses</span>
            <span class="fw-bold text-warning">{{ $inProgressTasks }}</span>
          </div>
          <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom-0">
            <span class="text-muted"><i class="ti ti-circle-filled text-danger me-2" style="font-size: 8px;"></i>Terlambat</span>
            <span class="fw-bold text-danger">{{ $overdueTasks }}</span>
          </div>
        </div>

        <div class="p-3 bg-light rounded text-center">
          <small class="text-muted d-block mb-1">Total Reimbursement Diajukan</small>
          <h5 class="mb-0 fw-bold">Rp{{ number_format($totalReimburse, 0, ',', '.') }}</h5>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
