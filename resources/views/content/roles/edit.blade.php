@extends('layouts/layoutMaster')

@section('title', 'Edit Master Role')

@section('content')
<div class="row">
  <div class="col-lg-8 mx-auto">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Role: {{ $role->name }}</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label" for="name">Role Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="E.g. Marketing Manager" value="{{ old('name', $role->name) }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" placeholder="Brief explanation about what this role does">{{ old('description', $role->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <hr class="my-4 mx-n4">

          <h5 class="mb-3">Menu Permissions</h5>
          <p class="text-muted">Select which menus this role is allowed to access from the sidebar.</p>
          
          <div class="row g-3">
            @foreach($menus as $menu)
              @if(!in_array($menu['slug'], ['admin.dashboard', 'pages-home']))
              <div class="col-md-6">
                <div class="form-check form-check-primary mt-3">
                  <input class="form-check-input" type="checkbox" name="menus[]" value="{{ $menu['slug'] }}" id="menu_{{ $menu['slug'] }}" 
                    {{ in_array($role->slug, $menu['roles']) ? 'checked' : '' }}>
                  <label class="form-check-label" for="menu_{{ $menu['slug'] }}">
                    <i class="{{ $menu['icon'] }} me-1"></i> {{ $menu['name'] }}
                  </label>
                </div>
              </div>
              @endif
            @endforeach
            
            @php
              $hasHome = false;
              $hasAdminDashboard = false;
              foreach($menus as $m) {
                  if ($m['slug'] === 'pages-home' && in_array($role->slug, $m['roles'])) $hasHome = true;
                  if ($m['slug'] === 'admin.dashboard' && in_array($role->slug, $m['roles'])) $hasAdminDashboard = true;
              }
            @endphp
            
            <div class="col-md-6">
                <div class="form-check form-check-primary mt-3">
                  <input class="form-check-input" type="checkbox" name="menus[]" value="pages-home" id="menu_pages-home" 
                    {{ $hasHome ? 'checked' : '' }}>
                  <label class="form-check-label" for="menu_pages-home">
                    <i class="menu-icon tf-icons ti ti-smart-home me-1"></i> General Dashboard
                  </label>
                  <small class="d-block text-muted">Mandatory for non-admin roles.</small>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-check-primary mt-3">
                  <input class="form-check-input" type="checkbox" name="menus[]" value="admin.dashboard" id="menu_admin-dashboard" 
                    {{ $hasAdminDashboard ? 'checked' : '' }}>
                  <label class="form-check-label" for="menu_admin-dashboard">
                    <i class="menu-icon tf-icons ti ti-smart-home me-1"></i> Admin Dashboard
                  </label>
                </div>
            </div>
          </div>

          <div class="mt-4 pt-2">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Update Role</button>
            <a href="{{ route('roles.index') }}" class="btn btn-label-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
