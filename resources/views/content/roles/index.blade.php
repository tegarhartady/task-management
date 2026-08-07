@extends('layouts/layoutMaster')

@section('title', 'Master Role')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <h4 class="fw-bold mb-0">Master Role</h4>
      <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Add New Role
      </a>
    </div>
  </div>

  <div class="col-12">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
    
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Role Name</th>
              <th>Slug (System ID)</th>
              <th>Description</th>
              <th>Created At</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $role)
            <tr>
              <td><span class="fw-semibold">{{ $role->name }}</span></td>
              <td><code>{{ $role->slug }}</code></td>
              <td>{{ $role->description ?? '-' }}</td>
              <td>{{ $role->created_at->format('d M Y') }}</td>
              <td class="text-center">
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill me-1" title="Edit">
                  <i class="ti ti-edit"></i>
                </a>
                
                @if(!in_array($role->slug, ['superadmin', 'admin']))
                <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this role?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-icon btn-text-danger rounded-pill" title="Delete">
                    <i class="ti ti-trash"></i>
                  </button>
                </form>
                @endif
              </td>
            </tr>
            @endforeach
            
            @if($roles->isEmpty())
            <tr>
              <td colspan="5" class="text-center py-4">No roles found.</td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
