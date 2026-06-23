{{-- filepath: resources/views/content/dashboards/supervisor-dashboard.blade.php --}}
@php
$configData = Helper::appClasses();

// Ensure all variables have defaults
$teamMembers = $teamMembers ?? collect([]);
$tasks = $tasks ?? [];
$briefs = $briefs ?? [];
$reimbursements = $reimbursements ?? [];
$today = $today ?? now()->format('Y-m-d');
$todayTasks = $todayTasks ?? 0;
$completedTasks = $completedTasks ?? 0;
$overdueTasks = $overdueTasks ?? 0;

// Collect tasks for filtering
$allTasks = collect($tasks);
$allBriefs = collect($briefs);
$allReimburse = collect($reimbursements);
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="container-fluid">
  <!-- Header Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="mb-1 fw-bold">Dashboard Supervisor 👋</h3>
          <p class="text-muted mb-0">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-2"></i> Assign Task
          </a>
          <a href="{{ route('pages-performance') }}" class="btn btn-outline-primary">
            <i class="ti ti-chart-bar me-2"></i> Performa Tim
          </a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="row g-3 mb-4">
        <!-- Team Members -->
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm" style="border-top: 4px solid #696cff;">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <p class="text-muted mb-2" style="font-size: 13px; font-weight: 500;">ANGGOTA TIM</p>
                  <h3 class="mb-0 fw-bold">{{ $teamMembers->count() }}</h3>
                  <small class="text-success"><i class="ti ti-point-filled me-1"></i>{{ $teamMembers->where('is_active', true)->count() }} Active</small>
                </div>
                <div class="avatar avatar-lg" style="background: linear-gradient(135deg, #696cff 0%, #4c51bf 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;">
                  <i class="ti ti-users text-white" style="font-size: 32px;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tasks Today -->
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm" style="border-top: 4px solid #ff9f43;">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <p class="text-muted mb-2" style="font-size: 13px; font-weight: 500;">TASK HARI INI</p>
                  <h3 class="mb-0 fw-bold">{{ $todayTasks }}</h3>
                  <small class="text-warning"><i class="ti ti-point-filled me-1"></i>Deadline hari ini</small>
                </div>
                <div class="avatar avatar-lg" style="background: linear-gradient(135deg, #ff9f43 0%, #ff8c2f 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;">
                  <i class="ti ti-calendar-event text-white" style="font-size: 32px;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Completed Tasks -->
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm" style="border-top: 4px solid #28c76f;">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <p class="text-muted mb-2" style="font-size: 13px; font-weight: 500;">SELESAI</p>
                  <h3 class="mb-0 fw-bold">{{ $completedTasks }}</h3>
                  <small class="text-success"><i class="ti ti-point-filled me-1"></i>{{ $allTasks->count() > 0 ? round(($completedTasks / $allTasks->count()) * 100) : 0 }}% completion</small>
                </div>
                <div class="avatar avatar-lg" style="background: linear-gradient(135deg, #28c76f 0%, #20a853 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;">
                  <i class="ti ti-check text-white" style="font-size: 32px;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Overdue Tasks -->
        <div class="col-lg-3 col-md-6">
          <div class="card h-100 shadow-sm" style="border-top: 4px solid #ea5455;">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <p class="text-muted mb-2" style="font-size: 13px; font-weight: 500;">TERLAMBAT</p>
                  <h3 class="mb-0 fw-bold text-danger">{{ $overdueTasks }}</h3>
                  <small class="text-danger"><i class="ti ti-point-filled me-1"></i>Perlu perhatian!</small>
                </div>
                <div class="avatar avatar-lg" style="background: linear-gradient(135deg, #ea5455 0%, #d63031 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; width: 70px; height: 70px;">
                  <i class="ti ti-alert-triangle text-white" style="font-size: 32px;"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Team Performance -->
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-light p-4 border-0">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h5 class="card-title mb-0 fw-bold"><i class="ti ti-users me-2"></i>Performa Tim</h5>
              <small class="text-muted">Monitor produktivitas anggota tim</small>
            </div>
            <button class="btn btn-sm btn-outline-primary">
              <i class="ti ti-refresh me-1"></i> Refresh
            </button>
          </div>
        </div>
        <div class="card-body p-4 bg-light">
          @forelse($teamMembers as $member)
            @php
              $memberTasks = $allTasks->where('assigned_to', $member->id);
              $completed = $memberTasks->where('status', 'Completed')->count();
              $inProgress = $memberTasks->where('status', 'In Progress')->count();
              $pending = $memberTasks->where('status', 'Not Started')->count();
              $total = $memberTasks->count();
              $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
              
              // Dynamic color based on performance
              $perfColor = '#696cff'; // Default
              if($percentage >= 80) $perfColor = '#28c76f';
              elseif($percentage >= 50) $perfColor = '#ff9f43';
              elseif($total > 0) $perfColor = '#ea5455';
            @endphp
            <div class="card mb-3 shadow-sm" style="border-left: 5px solid {{ $perfColor }};">
              <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md">
                      <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold">{{ $member->name }}</h6>
                      <small class="text-muted"><i class="ti ti-mail me-1"></i>{{ $member->email }}</small>
                    </div>
                  </div>
                  <div class="text-end">
                    <h5 class="mb-0 fw-bold text-primary">{{ $percentage }}%</h5>
                    <small class="text-muted small">Produktivitas</small>
                  </div>
                </div>
                
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <div class="progress mb-1" style="height: 8px; border-radius: 4px;">
                      <div class="progress-bar bg-success" style="width: {{ $total > 0 ? ($completed / $total * 100) : 0 }}%"></div>
                      <div class="progress-bar bg-warning" style="width: {{ $total > 0 ? ($inProgress / $total * 100) : 0 }}%"></div>
                      <div class="progress-bar bg-secondary" style="width: {{ $total > 0 ? ($pending / $total * 100) : 0 }}%"></div>
                    </div>
                    <div class="d-flex gap-3 small text-muted">
                      <span><span class="badge badge-dot bg-success me-1"></span>{{ $completed }} Selesai</span>
                      <span><span class="badge badge-dot bg-warning me-1"></span>{{ $inProgress }} Berlangsung</span>
                      <span><span class="badge badge-dot bg-secondary me-1"></span>{{ $pending }} Belum Mulai</span>
                    </div>
                  </div>
                  <div class="col-md-4 text-end">
                    <a href="{{ route('pages-performance.show', $member->id) }}" class="btn btn-sm btn-label-primary">
                      <i class="ti ti-eye me-1"></i> Detail
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <div class="text-center py-5 text-muted bg-white rounded shadow-sm">
              <i class="ti ti-users-off" style="font-size: 48px; opacity: 0.3;"></i>
              <p class="mt-3">Tidak ada anggota tim</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Quick Actions & Overdue -->
    <div class="col-lg-4">
      <!-- Quick Actions -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light p-4 border-0">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-bolt me-2"></i>Aksi Cepat</h5>
        </div>
        <div class="card-body p-4">
          <div class="d-grid gap-2">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
              <i class="ti ti-plus me-2"></i> Assign Task Baru
            </a>
            <a href="{{ route('pages-performance') }}" class="btn btn-outline-primary btn-sm">
              <i class="ti ti-chart-bar me-2"></i> Lihat Performa
            </a>
            <button class="btn btn-outline-secondary btn-sm">
              <i class="ti ti-message me-2"></i> Kirim Pesan
            </button>
            <button class="btn btn-outline-info btn-sm">
              <i class="ti ti-file-report me-2"></i> Buat Laporan
            </button>
          </div>
        </div>
      </div>

      <!-- Overdue Tasks -->
      <div class="card shadow-sm border-danger">
        <div class="card-header bg-danger text-white p-4 border-0">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold"><i class="ti ti-alert-triangle me-2"></i>Task Terlambat</h5>
            <span class="badge bg-white text-danger">{{ $overdueTasks }}</span>
          </div>
        </div>
        <div class="card-body p-4" style="max-height: 350px; overflow-y: auto;">
          @php
            $overdueTasks = $allTasks->filter(function($task) use ($today) {
              return $task->status !== 'Completed' && 
                     $task->due_date && 
                     $task->due_date < $today;
            });
          @endphp
          @forelse($overdueTasks as $task)
            <div class="mb-3 p-3 bg-danger bg-opacity-10 rounded-3">
              <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                  <h6 class="mb-1 fw-bold">{{ $task->title }}</h6>
                  <small class="text-muted d-block mb-2">{{ $task->assignedTo?->name ?? 'Unassigned' }}</small>
                  <small class="text-danger"><i class="ti ti-clock me-1"></i>{{ now()->diffInDays($task->due_date) }} hari terlambat</small>
                </div>
              </div>
              <div class="mt-3 pt-2 border-top border-danger border-opacity-25">
                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-danger w-100">
                  <i class="ti ti-eye me-1"></i> Lihat Detail
                </a>
              </div>
            </div>
          @empty
            <div class="text-center py-4 text-muted">
              <i class="ti ti-check-circle" style="font-size: 40px; opacity: 0.3;"></i>
              <p class="mt-2 mb-0">Tidak ada task terlambat</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- Additional Info -->
  <div class="row g-4 mt-2">
    <!-- Productivity Chart -->
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-light p-4 border-0">
          <h5 class="card-title mb-0 fw-bold"><i class="ti ti-chart-bar me-2"></i>Statistik Mingguan</h5>
        </div>
        <div class="card-body p-4">
          <div class="row text-center g-3">
            <div class="col-4">
              <div class="p-3 rounded-3" style="background: linear-gradient(135deg, rgba(40, 199, 111, 0.1) 0%, rgba(40, 199, 111, 0.05) 100%);">
                <h4 class="text-success mb-1 fw-bold">{{ $allTasks->where('status', 'Completed')->count() }}</h4>
                <small class="text-muted d-block">Selesai</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255, 159, 67, 0.1) 0%, rgba(255, 159, 67, 0.05) 100%);">
                <h4 class="text-warning mb-1 fw-bold">{{ $allTasks->where('status', 'In Progress')->count() }}</h4>
                <small class="text-muted d-block">Berlangsung</small>
              </div>
            </div>
            <div class="col-4">
              <div class="p-3 rounded-3" style="background: linear-gradient(135deg, rgba(105, 108, 255, 0.1) 0%, rgba(105, 108, 255, 0.05) 100%);">
                <h4 class="text-primary mb-1 fw-bold">{{ $allTasks->where('status', 'Not Started')->count() }}</h4>
                <small class="text-muted d-block">Belum Mulai</small>
              </div>
            </div>
          </div>
          <hr class="my-4">
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Completion Rate</span>
            <span class="badge bg-success">{{ $allTasks->count() > 0 ? round(($completedTasks / $allTasks->count()) * 100) : 0 }}%</span>
          </div>
          <div class="progress mt-3" style="height: 10px;">
            <div class="progress-bar bg-success" style="width: {{ $allTasks->count() > 0 ? round(($completedTasks / $allTasks->count()) * 100) : 0 }}%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity / Notes -->
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-light p-4 border-0">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold"><i class="ti ti-notes me-2"></i>Catatan</h5>
            <button class="btn btn-sm btn-outline-primary">
              <i class="ti ti-plus me-1"></i> Tambah
            </button>
          </div>
        </div>
        <div class="card-body p-4">
          <div class="mb-3 p-3 rounded-3" style="background: #f8f9ff; border-left: 4px solid #696cff;">
            <h6 class="mb-1 fw-bold">Reminder: Weekly Report</h6>
            <small class="text-muted d-block">Jangan lupa submit laporan mingguan sebelum jam 5 sore</small>
            <small class="text-muted mt-2 d-block"><i class="ti ti-clock me-1"></i>Hari ini</small>
          </div>
          <div class="mb-3 p-3 rounded-3" style="background: #fff8f0; border-left: 4px solid #ff9f43;">
            <h6 class="mb-1 fw-bold">Meeting dengan Client</h6>
            <small class="text-muted d-block">Persiapan presentasi untuk client Supernata besok</small>
            <small class="text-muted mt-2 d-block"><i class="ti ti-clock me-1"></i>Besok</small>
          </div>
          <div class="p-3 rounded-3" style="background: #ffe8e8; border-left: 4px solid #ea5455;">
            <h6 class="mb-1 fw-bold text-danger"><i class="ti ti-alert-circle me-1"></i>Priority: Project Dekornata</h6>
            <small class="text-muted d-block">Fokus ke project Dekornata minggu ini untuk mencapai deadline</small>
            <small class="text-muted mt-2 d-block"><i class="ti ti-clock me-1"></i>2 hari lalu</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Assign Task Modal -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Assign Task Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('tasks.store') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-bold">Judul Task</label>
            <input type="text" name="title" class="form-control" placeholder="Masukkan judul task" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Deskripsi</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan detail task..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Assign Ke</label>
            <select name="assigned_to" class="form-select" required>
              <option value="">Pilih Anggota Tim</option>
              @foreach($teamMembers as $member)
              <option value="{{ $member->id }}">{{ $member->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Prioritas</label>
              <select name="priority" class="form-select" required>
                <option value="Low">Low</option>
                <option value="Medium" selected>Medium</option>
                <option value="High">High</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Deadline</label>
              <input type="date" name="due_date" class="form-control">
            </div>
          </div>
          <div class="modal-footer border-0 pt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Assign Task</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
