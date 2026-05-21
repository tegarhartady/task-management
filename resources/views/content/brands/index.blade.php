@extends('layouts/layoutMaster')

@section('title', 'Master Brand')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Master Brand</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBrandModal">
          <i class="ti ti-plus me-1"></i> Add Brand
        </button>
      </div>
      
      @if(session('success'))
        <div class="alert alert-success mx-4">
          {{ session('success') }}
        </div>
      @endif

      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse($brands as $index => $brand)
              <tr>
                <td>{{ $brands->firstItem() + $index }}</td>
                <td><strong>{{ $brand->name }}</strong></td>
                <td>{{ Str::limit($brand->description, 50) ?? '-' }}</td>
                <td>
                  @if($brand->is_active)
                    <span class="badge bg-label-success">Active</span>
                  @else
                    <span class="badge bg-label-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex">
                    <button type="button" class="btn btn-sm btn-icon text-primary me-2" data-bs-toggle="modal" data-bs-target="#editBrandModal{{ $brand->id }}">
                      <i class="ti ti-edit"></i>
                    </button>
                    <form action="{{ route('brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon text-danger">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>

              <!-- Edit Modal -->
              <div class="modal fade" id="editBrandModal{{ $brand->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Brand</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('brands.update', $brand) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="name" value="{{ $brand->name }}" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" name="description" rows="3">{{ $brand->description }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-2">
                          <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch{{ $brand->id }}" {{ $brand->is_active ? 'checked' : '' }}>
                          <label class="form-check-label" for="activeSwitch{{ $brand->id }}">Active</label>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @empty
              <tr>
                <td colspan="5" class="text-center py-4">No brands found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="card-footer">
        {{ $brands->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createBrandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Brand</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('brands.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Brand Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" required placeholder="Enter brand name">
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3" placeholder="Enter description (optional)"></textarea>
          </div>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitchNew" checked>
            <label class="form-check-label" for="activeSwitchNew">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Brand</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
