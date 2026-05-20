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

// Admin statistics
$totalUsers = \App\Models\User::count();
$adminCount = \App\Models\User::where('role', 'admin')->count();
$supervisorCount = \App\Models\User::where('role', 'supervisor')->count();
$managerCount = \App\Models\User::where('role', 'manager')->count();
$karyawanCount = \App\Models\User::where('role', 'karyawan')->count();
$activeUsers = \App\Models\User::where('is_active', true)->count();

$totalReimburse = $allReimburse->sum('amount');
$approvedReimburse = $allReimburse->where('status', 'Approved')->sum('amount');
$pendingReimburse = $allReimburse->where('status', 'Pending')->sum('amount');
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
  <!-- Header -->
  <div class="col-12 mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-1">Selamat Datang, {{ auth()->user()->name }}! 👋</h4>
        <p class="text-muted mb-0">Dashboard Admin - System Overview</p>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">
          <i class="ti ti-download me-1"></i> Export
        </button>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Total Users</p>
          <h3 class="mb-0" style="color: #667eea;">{{ $totalUsers }}</h3>
        </div>
        <span class="badge bg-primary p-2">
          <i class="ti ti-users" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Active Users</p>
          <h3 class="mb-0" style="color: #28a745;">{{ $activeUsers }}</h3>
        </div>
        <span class="badge bg-success p-2">
          <i class="ti ti-user-check" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Total Tasks</p>
          <h3 class="mb-0" style="color: #ffc107;">{{ $allTasks->count() }}</h3>
        </div>
        <span class="badge bg-warning p-2">
          <i class="ti ti-list-check" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Pending Reimbursement</p>
          <h3 class="mb-0" style="color: #dc3545;">Rp{{ number_format($pendingReimburse, 0, ',', '.') }}</h3>
        </div>
        <span class="badge bg-danger p-2">
          <i class="ti ti-receipt" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <!-- User Breakdown -->
  <div class="col-lg-6 col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">User Breakdown by Role</h6>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-6 col-md-3 mb-3">
            <div class="p-3" style="background: rgba(102, 126, 234, 0.1); border-radius: 8px;">
              <h5 class="mb-1" style="color: #667eea;">{{ $adminCount }}</h5>
              <small class="text-muted">Admin</small>
            </div>
          </div>
          <div class="col-6 col-md-3 mb-3">
            <div class="p-3" style="background: rgba(255, 193, 7, 0.1); border-radius: 8px;">
              <h5 class="mb-1" style="color: #ffc107;">{{ $supervisorCount }}</h5>
              <small class="text-muted">Supervisor</small>
            </div>
          </div>
          <div class="col-6 col-md-3 mb-3">
            <div class="p-3" style="background: rgba(40, 167, 69, 0.1); border-radius: 8px;">
              <h5 class="mb-1" style="color: #28a745;">{{ $managerCount }}</h5>
              <small class="text-muted">Manager</small>
            </div>
          </div>
          <div class="col-6 col-md-3 mb-3">
            <div class="p-3" style="background: rgba(13, 202, 240, 0.1); border-radius: 8px;">
              <h5 class="mb-1" style="color: #0dcaf0;">{{ $karyawanCount }}</h5>
              <small class="text-muted">Karyawan</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Summary -->
  <div class="col-lg-6 col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Reimbursement Summary</h6>
      </div>
      <div class="card-body">
        <div class="mb-3 pb-3" style="border-bottom: 1px solid #eee;">
          <div class="d-flex justify-content-between align-items-center">
            <span>Total Reimbursement</span>
            <strong>Rp{{ number_format($totalReimburse, 0, ',', '.') }}</strong>
          </div>
        </div>
        <div class="mb-3 pb-3" style="border-bottom: 1px solid #eee;">
          <div class="d-flex justify-content-between align-items-center">
            <span>Approved</span>
            <strong style="color: #28a745;">Rp{{ number_format($approvedReimburse, 0, ',', '.') }}</strong>
          </div>
        </div>
        <div>
          <div class="d-flex justify-content-between align-items-center">
            <span>Pending</span>
            <strong style="color: #dc3545;">Rp{{ number_format($pendingReimburse, 0, ',', '.') }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Tasks -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Recent Tasks</h6>
        <a href="{{ route('pages-tasks') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Deadline</th>
              </tr>
            </thead>
            <tbody>
              @forelse($allTasks->take(5) as $task)
                <tr>
                  <td>
                    <h6 class="mb-0">{{ $task['title'] }}</h6>
                  </td>
                  <td>
                    @if($task['priority'] === 'High')
                      <span class="badge bg-danger">{{ $task['priority'] }}</span>
                    @elseif($task['priority'] === 'Medium')
                      <span class="badge bg-warning">{{ $task['priority'] }}</span>
                    @else
                      <span class="badge bg-info">{{ $task['priority'] }}</span>
                    @endif
                  </td>
                  <td>
                    @if($task['status'] === 'Completed')
                      <span class="badge bg-success">{{ $task['status'] }}</span>
                    @elseif($task['status'] === 'In Progress')
                      <span class="badge bg-warning">{{ $task['status'] }}</span>
                    @else
                      <span class="badge bg-secondary">{{ $task['status'] }}</span>
                    @endif
                  </td>
                  <td><small class="text-muted">{{ $task['deadline'] ?? 'N/A' }}</small></td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">No tasks</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
