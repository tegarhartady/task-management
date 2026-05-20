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

$totalTasks = $allTasks->count();
$overdueTasks = $allTasks->where('status', '!=', 'Completed')
    ->where('due_date', '<', date('Y-m-d'))
    ->count();

$totalReimburse = $allReimburse->sum('amount');
$approvedReimburse = $allReimburse->where('status', 'Approved')->sum('amount');
$pendingReimburse = $allReimburse->where('status', 'Pending')->sum('amount');
$rejectedReimburse = $allReimburse->where('status', 'Rejected')->sum('amount');

// Get all users
$users = \App\Models\User::all();
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
          <h3 class="mb-0" style="color: #ffc107;">{{ $totalTasks }}</h3>
        </div>
        <span class="badge bg-warning p-2">
          <i class="ti ti-list-check" style="font-size: 20px;"></i>
        </span>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-12 mb-4">
    <div class="card h-100 border-start border-danger border-4">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <p class="mb-1">Overdue Tasks</p>
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
  
  {{-- <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
    <div class="card h-100 border-start border-success border-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block mb-1">Total Task</small>
            <h4 class="mb-0">{{ $allTasks->count() }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial bg-label-success rounded">
              <i class="ti ti-list-check ti-md"></i>
            </span>
          </div>
        </div>
        <div class="mt-2">
          <small class="text-success"><i class="ti ti-trending-up me-1"></i>+15% bulan ini</small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
    <div class="card h-100 border-start border-warning border-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block mb-1">Content Brief</small>
            <h4 class="mb-0">{{ $allBriefs->count() }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial bg-label-warning rounded">
              <i class="ti ti-book ti-md"></i>
            </span>
          </div>
        </div>
        <div class="mt-2">
          <small class="text-warning"><i class="ti ti-trending-up me-1"></i>+8% bulan ini</small>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
    <div class="card h-100 border-start border-danger border-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted d-block mb-1">Total Reimbursement</small>
            <h4 class="mb-0">Rp{{ number_format($totalReimburse, 0, ',', '.') }}</h4>
          </div>
          <div class="avatar">
            <span class="avatar-initial bg-label-danger rounded">
              <i class="ti ti-currency-dollar ti-md"></i>
            </span>
          </div>
        </div>
        <div class="mt-2">
          <small class="text-danger"><i class="ti ti-trending-down me-1"></i>-5% bulan ini</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- User Management -->
  <div class="col-xl-8 col-md-12 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="ti ti-users me-2"></i>Manajemen User</h5>
        <div>
          <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="ti ti-plus me-1"></i> Tambah User
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Role</th>
                <th>Status</th>
                <th>Login Terakhir</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                      <span class="avatar-initial bg-label-primary rounded-circle">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <span>{{ $user->name }}</span>
                  </div>
                </td>
                <td>
                  <span class="badge 
                    @if($user->role == 'admin') bg-danger
                    @elseif($user->role == 'supervisor') bg-warning
                    @elseif($user->role == 'manager') bg-primary
                    @else bg-secondary @endif
                  ">{{ ucfirst($user->role) }}</span>
                </td>
                <td>
                  <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td><small class="text-muted">{{ $user->last_login_at ?? 'N/A' }}</small></td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown">
                      <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#"><i class="ti ti-eye me-1"></i> Lihat Detail</a></li>
                      <li><a class="dropdown-item" href="#"><i class="ti ti-edit me-1"></i> Edit</a></li>
                      <li><a class="dropdown-item" href="#"><i class="ti ti-key me-1"></i> Reset Password</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-1"></i> Hapus</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- System Status & Quick Actions -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0"><i class="ti ti-server me-2"></i>Status Sistem</h5>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-2">
              <span class="avatar-initial bg-success rounded-circle">
                <i class="ti ti-server ti-xs"></i>
              </span>
            </div>
            <span>Server</span>
          </div>
          <span class="badge bg-success">Online</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-2">
              <span class="avatar-initial bg-success rounded-circle">
                <i class="ti ti-database ti-xs"></i>
              </span>
            </div>
            <span>Database</span>
          </div>
          <span class="badge bg-success">Connected</span>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-2">
              <span class="avatar-initial bg-success rounded-circle">
                <i class="ti ti-shield ti-xs"></i>
              </span>
            </div>
            <span>Keamanan</span>
          </div>
          <span class="badge bg-success">Aman</span>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-2">
              <span class="avatar-initial bg-warning rounded-circle">
                <i class="ti ti-cloud ti-xs"></i>
              </span>
            </div>
            <span>Backup</span>
          </div>
          <span class="badge bg-warning">12 jam lalu</span>
        </div>
        
        <hr class="my-3">
        
        <div class="d-grid gap-2">
          <button class="btn btn-outline-primary btn-sm">
            <i class="ti ti-database-export me-1"></i> Backup Sekarang
          </button>
          <button class="btn btn-outline-danger btn-sm">
            <i class="ti ti-trash me-1"></i> Clear Cache
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Financial Overview -->
  <div class="col-xl-6 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="ti ti-report-money me-2"></i>Laporan Keuangan</h5>
        <a href="{{ url('pages-reimburs') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <small class="text-muted d-block">Disetujui</small>
            <h5 class="mb-0 text-success">Rp{{ number_format($approvedReimburse, 0, ',', '.') }}</h5>
          </div>
          <div class="avatar">
            <span class="avatar-initial bg-label-success rounded">
              <i class="ti ti-check"></i>
            </span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <small class="text-muted d-block">Menunggu</small>
            <h5 class="mb-0 text-warning">Rp{{ number_format($pendingReimburse, 0, ',', '.') }}</h5>
          </div>
          <div class="avatar">
            <span class="avatar-initial bg-label-warning rounded">
              <i class="ti ti-clock"></i>
            </span>
          </div>
        </div>
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <small class="text-muted d-block">Ditolak</small>
            <h5 class="mb-0 text-danger">Rp{{ number_format($rejectedReimburse, 0, ',', '.') }}</h5>
          </div>
          <div class="avatar">
            <span class="avatar-initial bg-label-danger rounded">
              <i class="ti ti-x"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Audit Log -->
  <div class="col-xl-6 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="ti ti-history me-2"></i>Log Aktivitas</h5>
        <button class="btn btn-sm btn-outline-primary">Lihat Semua</button>
      </div>
      <div class="card-body">
        <div class="d-flex mb-3">
          <div class="avatar avatar-sm me-2 flex-shrink-0">
            <span class="avatar-initial bg-danger rounded-circle">
              <i class="ti ti-user-plus ti-xs"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <small class="fw-medium d-block">User baru ditambahkan</small>
            <small class="text-muted">Admin menambahkan user "Budi"</small>
            <small class="text-muted d-block">2 jam lalu</small>
          </div>
        </div>
        <div class="d-flex mb-3">
          <div class="avatar avatar-sm me-2 flex-shrink-0">
            <span class="avatar-initial bg-success rounded-circle">
              <i class="ti ti-check ti-xs"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <small class="fw-medium d-block">Reimbursement disetujui</small>
            <small class="text-muted">Rp150.000 untuk Dila</small>
            <small class="text-muted d-block">4 jam lalu</small>
          </div>
        </div>
        <div class="d-flex">
          <div class="avatar avatar-sm me-2 flex-shrink-0">
            <span class="avatar-initial bg-primary rounded-circle">
              <i class="ti ti-settings ti-xs"></i>
            </span>
          </div>
          <div class="flex-grow-1">
            <small class="fw-medium d-block">Pengaturan diubah</small>
            <small class="text-muted">Email notification diaktifkan</small>
            <small class="text-muted d-block">1 hari lalu</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" placeholder="Masukkan nama">
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" placeholder="Masukkan email">
        </div>
        <div class="mb-3">
          <label class="form-label">Role</label>
          <select class="form-select">
            <option>User</option>
            <option>Manager</option>
            <option>Supervisor</option>
            <option>Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" placeholder="Masukkan password">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- System Settings Modal -->
<div class="modal fade" id="systemSettingsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pengaturan Sistem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Nama Aplikasi</label>
            <input type="text" class="form-control" value="Nata Work">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Timezone</label>
            <select class="form-select">
              <option selected>Asia/Jakarta (WIB)</option>
              <option>Asia/Makassar (WITA)</option>
              <option>Asia/Jayapura (WIT)</option>
            </select>
          </div>
          <div class="col-12 mb-3">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="emailNotif" checked>
              <label class="form-check-label" for="emailNotif">Email Notification</label>
            </div>
          </div>
          <div class="col-12 mb-3">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="autoBackup" checked>
              <label class="form-check-label" for="autoBackup">Auto Backup (Harian)</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary">Simpan Pengaturan</button>
      </div>
    </div>
  </div>
</div>
@endsection --}}