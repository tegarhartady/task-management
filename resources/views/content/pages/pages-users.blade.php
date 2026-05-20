@php
$configData = Helper::appClasses();

$roleColors = [
  'superadmin' => '#7367f0',
  'admin' => '#696cff',
  'supervisor' => '#ff9f43',
  'manager' => '#28c76f',
  'karyawan' => '#ea5455',
];

// Stats
$allUsers = \App\Models\User::all();
$countAll = $allUsers->count();
$countSuperadmin = $allUsers->where('role', 'superadmin')->count();
$countAdmin = $allUsers->where('role', 'admin')->count();
$countSupervisor = $allUsers->where('role', 'supervisor')->count();
$countManager = $allUsers->where('role', 'manager')->count();
$countKaryawan = $allUsers->where('role', 'karyawan')->count();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'User Management')

@section('content')
<div class="row">
  <div class="col-12 d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">User Management 👥</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
      <i class="ti ti-plus me-2"></i> Add New User
    </button>
  </div>
</div>

<!-- Alert Messages -->
@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- Stats Cards -->
<div class="row mb-3">
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 shadow-sm">
      <div class="card-body py-3">
        <h3 class="mb-0">{{ $countAll }}</h3>
        <small class="text-muted">Total Users</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 shadow-sm">
      <div class="card-body py-3">
        <h3 class="mb-0" style="color: {{ $roleColors['superadmin'] }}">{{ $countSuperadmin }}</h3>
        <small class="text-muted">Superadmin</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 shadow-sm">
      <div class="card-body py-3">
        <h3 class="mb-0" style="color: {{ $roleColors['admin'] }}">{{ $countAdmin }}</h3>
        <small class="text-muted">Admin</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 shadow-sm">
      <div class="card-body py-3">
        <h3 class="mb-0" style="color: {{ $roleColors['supervisor'] }}">{{ $countSupervisor }}</h3>
        <small class="text-muted">Supervisor</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 shadow-sm">
      <div class="card-body py-3">
        <h3 class="mb-0" style="color: {{ $roleColors['manager'] }}">{{ $countManager }}</h3>
        <small class="text-muted">Manager</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100 shadow-sm">
      <div class="card-body py-3">
        <h3 class="mb-0" style="color: {{ $roleColors['karyawan'] }}">{{ $countKaryawan }}</h3>
        <small class="text-muted">Karyawan</small>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ route('users.index') }}" class="mb-3">
  <div class="card mb-3 shadow-sm">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
      <select class="form-select w-auto" name="role" onchange="this.form.submit()">
        <option value="">All Roles</option>
        <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
        <option value="karyawan" {{ request('role') == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
      </select>
      <div class="flex-grow-1">
        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search users by name or email..." onkeydown="if(event.key==='Enter'){this.form.submit();}">
      </div>
      <button type="submit" class="btn btn-outline-secondary">Filter</button>
      @if(request('role') || request('search'))
        <a href="{{ route('users.index') }}" class="btn btn-link">Reset</a>
      @endif
    </div>
  </div>
</form>

<!-- Users List -->
<div class="row">
  <div class="col-12">
    @forelse($users as $user)
      @php
        $borderColor = $roleColors[$user->role] ?? '#ccc';
      @endphp
      <div class="card mb-3 shadow-sm" style="border-left: 4px solid {{ $borderColor }}; transition: transform 0.2s;">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center" style="max-width: 70%;">
            <div class="avatar avatar-md me-3 bg-label-{{ $user->role === 'superadmin' ? 'primary' : ($user->role === 'admin' ? 'info' : ($user->role === 'supervisor' ? 'warning' : ($user->role === 'manager' ? 'success' : 'danger'))) }} p-2" style="border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
              {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
              <h6 class="mb-1 text-body fw-bold">{{ $user->name }}</h6>
              <p class="text-muted mb-2 small">{{ $user->email }}</p>
              <div class="d-flex flex-wrap gap-1">
                <span class="badge" style="background-color: {{ $borderColor }}">
                  {{ ucfirst($user->role) }}
                </span>
                @if($user->is_active)
                  <span class="badge bg-label-success">Active</span>
                @else
                  <span class="badge bg-label-secondary">Inactive</span>
                @endif
              </div>
            </div>
          </div>
          <div class="text-end">
            <div class="btn-group" role="group">
              <button class="btn btn-sm btn-label-primary edit-user-btn"
                data-user="{{ json_encode($user) }}"
                data-bs-toggle="modal" data-bs-target="#editUserModal" title="Edit">
                <i class="ti ti-edit"></i>
              </button>
              @if($user->id !== auth()->id())
                <button type="button" class="btn btn-sm btn-label-warning toggle-status"
                  data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" title="Toggle Status">
                  <i class="ti ti-power"></i>
                </button>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-label-danger" title="Delete">
                    <i class="ti ti-trash"></i>
                  </button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="card shadow-sm">
        <div class="card-body text-center py-5 text-muted">
          <i class="ti ti-users" style="font-size: 3rem; opacity: 0.5;"></i>
          <p class="mt-3 mb-0">No users found</p>
        </div>
      </div>
    @endforelse

    <!-- Pagination -->
    <div class="mt-4">
      {{ $users->appends(request()->query())->links() }}
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required placeholder="Full Name">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="email@example.com">
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="superadmin">Superadmin</option>
              <option value="admin">Admin</option>
              <option value="supervisor">Supervisor</option>
              <option value="manager">Manager</option>
              <option value="karyawan" selected>Karyawan</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="••••••••">
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editUserForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" id="edit_role" class="form-select" required>
              <option value="superadmin">Superadmin</option>
              <option value="admin">Admin</option>
              <option value="supervisor">Supervisor</option>
              <option value="manager">Manager</option>
              <option value="karyawan">Karyawan</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="is_active" id="edit_is_active" class="form-select" required>
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <hr>
          <small class="text-muted">Leave password blank if you don't want to change it.</small>
          <div class="mb-3 mt-2">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••">
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  .btn-label-primary {
    background-color: rgba(105, 108, 255, 0.1);
    color: #696cff;
    border: none;
  }
  .btn-label-warning {
    background-color: rgba(255, 159, 67, 0.1);
    color: #ff9f43;
    border: none;
  }
  .btn-label-danger {
    background-color: rgba(234, 84, 85, 0.1);
    color: #ea5455;
    border: none;
  }
</style>

<script>
  // Handle Edit User Modal
  document.querySelectorAll('.edit-user-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const user = JSON.parse(this.dataset.user);
      const form = document.getElementById('editUserForm');

      form.action = `/users/${user.id}`;
      document.getElementById('edit_name').value = user.name;
      document.getElementById('edit_email').value = user.email;
      document.getElementById('edit_role').value = user.role;
      document.getElementById('edit_is_active').value = user.is_active;
    });
  });

  // Handle Toggle Status
  document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
      const userId = this.dataset.userId;
      const userName = this.dataset.userName;

      if (!confirm(`Toggle status for ${userName}?`)) return;

      fetch(`/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert(data.error || 'Error updating status');
          }
        })
        .catch(err => console.error('Error:', err));
    });
  });
</script>
@endsection
