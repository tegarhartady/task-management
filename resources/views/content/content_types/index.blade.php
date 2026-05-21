@extends('layouts/layoutMaster')

@section('title', 'Master Content Type')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Master Content Type</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContentTypeModal">
          <i class="ti ti-plus me-1"></i> Add Content Type
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
            @forelse($contentTypes as $index => $type)
              <tr>
                <td>{{ $contentTypes->firstItem() + $index }}</td>
                <td><strong>{{ $type->name }}</strong></td>
                <td>{{ Str::limit($type->description, 50) ?? '-' }}</td>
                <td>
                  @if($type->is_active)
                    <span class="badge bg-label-success">Active</span>
                  @else
                    <span class="badge bg-label-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex">
                    <button type="button" class="btn btn-sm btn-icon text-primary me-2" data-bs-toggle="modal" data-bs-target="#editContentTypeModal{{ $type->id }}">
                      <i class="ti ti-edit"></i>
                    </button>
                    <form action="{{ route('content_types.destroy', $type) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this content type?');">
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
              <div class="modal fade" id="editContentTypeModal{{ $type->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Content Type</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('content_types.update', $type) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Content Type Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="name" value="{{ $type->name }}" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" name="description" rows="3">{{ $type->description }}</textarea>
                        </div>
                        <div class="form-check form-switch mb-2">
                          <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch{{ $type->id }}" {{ $type->is_active ? 'checked' : '' }}>
                          <label class="form-check-label" for="activeSwitch{{ $type->id }}">Active</label>
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
                <td colspan="5" class="text-center py-4">No content types found</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <div class="card-footer">
        {{ $contentTypes->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createContentTypeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Content Type</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('content_types.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Content Type Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" required placeholder="Enter content type name (e.g. Video, Article)">
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
          <button type="submit" class="btn btn-primary">Save Content Type</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
