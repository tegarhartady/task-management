@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="mb-1 fw-bold">Edit User ✏️</h3>
          <p class="text-muted mb-0">Update user information</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-2"></i> Back
        </a>
      </div>
    </div>
  </div>

  <!-- Form -->
  <div class="row">
    <div class="col-lg-8 mx-auto">
      <div class="card">
        <div class="card-body p-4">
          <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="name">Full Name</label>
              <input 
                type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name', $user->name) }}"
                placeholder="Enter full name"
                required
              >
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="email">Email Address</label>
              <input 
                type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                id="email" 
                name="email" 
                value="{{ old('email', $user->email) }}"
                placeholder="Enter email address"
                required
              >
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="password">New Password <small class="text-muted">(Leave blank to keep current)</small></label>
              <input 
                type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                id="password" 
                name="password" 
                placeholder="Enter new password (min 8 characters)"
              >
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="password_confirmation">Confirm Password</label>
              <input 
                type="password" 
                class="form-control" 
                id="password_confirmation" 
                name="password_confirmation" 
                placeholder="Confirm new password"
              >
            </div>

            <!-- Role -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="role">Role</label>
              <select 
                class="form-select @error('role') is-invalid @enderror" 
                id="role" 
                name="role" 
                required
              >
                <option value="">Select a role</option>
                <option value="admin" @if(old('role', $user->role) === 'admin') selected @endif>Admin</option>
                <option value="supervisor" @if(old('role', $user->role) === 'supervisor') selected @endif>Supervisor</option>
                <option value="manager" @if(old('role', $user->role) === 'manager') selected @endif>Manager</option>
                <option value="karyawan" @if(old('role', $user->role) === 'karyawan') selected @endif>Karyawan</option>
              </select>
              @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Active Status -->
            <div class="mb-4">
              <div class="form-check">
                <input 
                  type="checkbox" 
                  class="form-check-input" 
                  id="is_active" 
                  name="is_active" 
                  value="1"
                  @if(old('is_active', $user->is_active)) checked @endif
                >
                <label class="form-check-label fw-semibold" for="is_active">
                  Active User
                </label>
              </div>
              <small class="text-muted">Uncheck to deactivate this user</small>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info mb-4" role="alert">
              <i class="ti ti-info-circle me-2"></i>
              <strong>Email:</strong> {{ $user->email }}<br>
              <strong>Created:</strong> {{ $user->created_at->format('d M Y H:i') }}<br>
              <strong>Last Updated:</strong> {{ $user->updated_at->format('d M Y H:i') }}
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-2"></i> Update User
              </button>
              <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-x me-2"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
