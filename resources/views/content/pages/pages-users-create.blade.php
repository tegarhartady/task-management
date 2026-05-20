@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h3 class="mb-1 fw-bold">Create New User ➕</h3>
          <p class="text-muted mb-0">Add a new user to the system</p>
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
          <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="name">Full Name</label>
              <input 
                type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name') }}"
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
                value="{{ old('email') }}"
                placeholder="Enter email address"
                required
              >
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label fw-semibold" for="password">Password</label>
              <input 
                type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                id="password" 
                name="password" 
                placeholder="Enter password (min 8 characters)"
                required
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
                placeholder="Confirm password"
                required
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
                <option value="admin" @if(old('role') === 'admin') selected @endif>Admin</option>
                <option value="supervisor" @if(old('role') === 'supervisor') selected @endif>Supervisor</option>
                <option value="manager" @if(old('role') === 'manager') selected @endif>Manager</option>
                <option value="karyawan" @if(old('role') === 'karyawan') selected @endif>Karyawan</option>
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
                  @if(old('is_active', true)) checked @endif
                >
                <label class="form-check-label fw-semibold" for="is_active">
                  Active User
                </label>
              </div>
              <small class="text-muted">Uncheck to create an inactive user</small>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="ti ti-check me-2"></i> Create User
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
