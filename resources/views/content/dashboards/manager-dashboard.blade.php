@php
$configData = Helper::appClasses();

// Load all data
$tasksJson = file_get_contents(resource_path('data/tasks.json'));
$tasksData = json_decode($tasksJson, true);
$briefsJson = file_get_contents(resource_path('data/briefs.json'));
$briefsData = json_decode($briefsJson, true);
$reimbursJson = file_get_contents(resource_path('data/reimburs.json'));
$reimbursData = json_decode($reimbursJson, true);

// Calculate statistics
$allTasks = collect($tasksData['tasks']);
$allBriefs = collect($briefsData)->flatMap(function($v) { return $v['items']; });
$allReimburse = collect($reimbursData)->flatMap(function($v) { return $v['items']; });

// Manager specific data
$totalReimburse = $allReimburse->sum('amount');
$approvedReimburse = $allReimburse->where('status', 'Approved')->sum('amount');
$pendingReimburse = $allReimburse->where('status', 'Pending')->sum('amount');

$teamCount = \App\Models\User::where('role', 'karyawan')->count();
$activeTeamMembers = \App\Models\User::where('role', 'karyawan')->where('is_active', true)->count();
$overdueTasks = $allTasks->where('status', '!=', 'Completed')->where('due_date', '<', date('Y-m-d'))->count();

// Team members (dummy)
$teamMembers = [
  ['name' => 'Dila', 'role' => 'Karyawan', 'status' => 'Active', 'performance' => 95],
  ['name' => 'Budi', 'role' => 'Karyawan', 'status' => 'Active', 'performance' => 87],
  ['name' => 'Siti', 'role' => 'Karyawan', 'status' => 'Active', 'performance' => 92],
  ['name' => 'Ahmad', 'role' => 'Karyawan', 'status' => 'Inactive', 'performance' => 78],
];
$teamMembers = collect($teamMembers);
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Manager Dashboard')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1">Selamat Datang, Manager! 👋</h4>
        <p class="text-muted mb-0">Manajemen tim dan monitoring performa</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">
          <i class="ti ti-download me-1"></i> Export Laporan
        </button>
        <button class="btn btn-primary btn-sm">
          <i class="ti ti-plus me-1"></i> Tugas Baru
        </button>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Total Anggota Tim</p>
          <h3 class="mb-0" style="color: #667eea;">{{ $teamCount }}</h3>
        </div>
        <span class="badge bg-info p-2">
          <i class="ti ti-users" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Tim Aktif</p>
          <h3 class="mb-0" style="color: #28a745;">{{ $activeTeamMembers }}</h3>
        </div>
        <span class="badge bg-success p-2">
          <i class="ti ti-user-check" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100 border-start border-danger border-4">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Tugas Terlambat</p>
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
          <p class="mb-1">Reimbursement Pending</p>
          <h3 class="mb-0" style="color: #dc3545;">Rp{{ number_format($pendingReimburse, 0, ',', '.') }}</h3>
        </div>
        <span class="badge bg-danger p-2">
          <i class="ti ti-receipt" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <!-- Team Performance Chart -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Performa Tim</h6>
        <small class="text-muted">Update Real-time</small>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Nama</th>
                <th>Status</th>
                <th>Performa</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($teamMembers as $member)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img src="{{ asset('assets/img/avatars/1.png') }}" alt="avatar" class="rounded-circle" width="40" />
                      <div>
                        <h6 class="mb-0">{{ $member['name'] }}</h6>
                        <small class="text-muted">{{ $member['role'] }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($member['status'] === 'Active')
                      <span class="badge bg-success">{{ $member['status'] }}</span>
                    @else
                      <span class="badge bg-secondary">{{ $member['status'] }}</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $member['performance'] }}%" aria-valuenow="{{ $member['performance'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <small>{{ $member['performance'] }}%</small>
                    </div>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" title="View Details">
                      <i class="ti ti-eye"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">Tidak ada data anggota tim</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Summary -->
  <div class="col-lg-6 col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Ringkasan Reimbursement</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6 mb-3">
            <p class="text-muted mb-1">Total Reimbursement</p>
            <h5 class="mb-0">Rp{{ number_format($totalReimburse, 0, ',', '.') }}</h5>
          </div>
          <div class="col-6 mb-3">
            <p class="text-muted mb-1">Approved</p>
            <h5 class="mb-0" style="color: #28a745;">Rp{{ number_format($approvedReimburse, 0, ',', '.') }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Tasks -->
  <div class="col-lg-6 col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Tugas Terbaru</h6>
      </div>
      <div class="card-body">
        @forelse($allTasks->take(5) as $task)
          <div class="mb-3 pb-3" style="border-bottom: 1px solid #eee;">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 class="mb-0">{{ $task['title'] }}</h6>
              <span class="badge {{ $task['status'] === 'Completed' ? 'bg-success' : ($task['status'] === 'In Progress' ? 'bg-warning' : 'bg-secondary') }}">
                {{ $task['status'] }}
              </span>
            </div>
            <small class="text-muted">{{ $task['priority'] }} Priority</small>
          </div>
        @empty
          <p class="text-muted mb-0">Tidak ada tugas</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
