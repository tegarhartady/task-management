{{-- filepath: resources/views/content/pages/Tasks/index.blade.php --}}
@php
$configData = Helper::appClasses();

// Load tasks data from JSON
$tasksJson = file_get_contents(resource_path('data/tasks.json'));
$tasksData = json_decode($tasksJson, true);

// Get filter values from request
$filterStatus = request('status');
$filterBrand = request('brand');
$filterCategory = request('category');
$search = request('search');

// Collect all tasks
$allTasks = collect($tasksData['tasks']);

// Filter by status
if ($filterStatus && $filterStatus !== 'All') {
  $allTasks = $allTasks->filter(function($task) use ($filterStatus) {
    return strtolower($task['status']) === strtolower($filterStatus);
  });
}

// Filter by brand
if ($filterBrand && $filterBrand !== 'All') {
  $allTasks = $allTasks->filter(function($task) use ($filterBrand) {
    return strtolower($task['brand']) === strtolower($filterBrand);
  });
}

// Filter by category
if ($filterCategory && $filterCategory !== 'All') {
  $allTasks = $allTasks->filter(function($task) use ($filterCategory) {
    return strtolower($task['category']) === strtolower($filterCategory);
  });
}

// Search
if ($search) {
  $allTasks = $allTasks->filter(function($task) use ($search) {
    return str_contains(strtolower($task['title']), strtolower($search)) ||
           str_contains(strtolower($task['description']), strtolower($search));
  });
}

// Pagination
$allTasks = $allTasks->values();
$page = (int) request('page', 1);
$perPage = 5;
$total = $allTasks->count();
$lastPage = max(ceil($total / $perPage), 1);
$page = min(max($page, 1), $lastPage);
$tasks = $allTasks->slice(($page - 1) * $perPage, $perPage);

// Status color mapping
$statusColors = [
  'completed' => '#28c76f',
  'in progress' => '#ff9f43',
  'pending' => '#7367f0',
  'on hold' => '#ea5455',
];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Work Tasks')

@section('content')
<div class="row">
  <div class="col-12 d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Work Tasks</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">+ Create Task</button>
  </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="taskTitle" class="form-label">Title</label>
            <input type="text" class="form-control" id="taskTitle" placeholder="Enter task title" required>
          </div>
          <div class="mb-3">
            <label for="taskDescription" class="form-label">Description</label>
            <textarea class="form-control" id="taskDescription" rows="3" placeholder="Describe the task..." required></textarea>
          </div>
          <div class="mb-3">
            <label for="taskCategory" class="form-label">Category</label>
            <select class="form-select" id="taskCategory" required>
              <option value="">Select Category</option>
              <option>Graphic Design</option>
              <option>3D Modelling</option>
              <option>Content Writing</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="taskBrand" class="form-label">Brand</label>
            <select class="form-select" id="taskBrand" required>
              <option>Supernata</option>
              <option>Dekornata</option>
              <option>Craftnata</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="taskPriority" class="form-label">Priority (Difficulty)</label>
            <select class="form-select" id="taskPriority" required>
              <option value="">Select Priority</option>
              <option>Trivial</option>
              <option>Easy</option>
              <option>Medium</option>
              <option>Hard</option>
              <option>Complex</option>
            </select>
            <div class="card mt-2 border shadow-sm">
              <div class="card-body py-2 px-3">
                <strong>Difficulty Scoring Guide:</strong>
                <ul class="mb-0 ps-3" style="list-style:none;">
                  <li class="mb-1 d-flex align-items-center">
                    <span class="badge bg-light text-dark border me-2" style="min-width:70px;">Trivial</span>
                    <span>Sangat mudah, bisa selesai &lt; 30 menit</span>
                  </li>
                  <li class="mb-1 d-flex align-items-center">
                    <span class="badge bg-success me-2" style="min-width:70px;">Easy</span>
                    <span>Mudah, selesai &lt; 2 jam</span>
                  </li>
                  <li class="mb-1 d-flex align-items-center">
                    <span class="badge bg-primary me-2" style="min-width:70px;">Medium</span>
                    <span>Normal, selesai &lt; 1 hari kerja</span>
                  </li>
                  <li class="mb-1 d-flex align-items-center">
                    <span class="badge bg-warning text-dark me-2" style="min-width:70px;">Hard</span>
                    <span>Sulit, selesai &lt; 2 hari kerja</span>
                  </li>
                  <li class="d-flex align-items-center">
                    <span class="badge bg-danger me-2" style="min-width:70px;">Complex</span>
                    <span>Sangat sulit, butuh 3 hari kerja atau lebih</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="taskDueDate" class="form-label">Due Date</label>
            <input type="date" class="form-control" id="taskDueDate" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Task</button>
        </div>
      </form>
    </div>
  </div>
</div>

@php
  $allTasksForCount = collect($tasksData['tasks']);
  $countCompleted = $allTasksForCount->where('status', 'Completed')->count();
  $countInProgress = $allTasksForCount->where('status', 'In Progress')->count();
  $countPending = $allTasksForCount->where('status', 'Pending')->count();
  $countOnHold = $allTasksForCount->where('status', 'On Hold')->count();
@endphp
<div class="row mb-3">
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0">{{ $allTasksForCount->count() }}</h3>
        <small class="text-muted">All Tasks</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-success">{{ $countCompleted }}</h3>
        <small class="text-muted">Completed</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-warning">{{ $countInProgress }}</h3>
        <small class="text-muted">In Progress</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-primary">{{ $countPending }}</h3>
        <small class="text-muted">Not Started</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md mb-2">
    <div class="card text-center h-100">
      <div class="card-body py-3">
        <h3 class="mb-0 text-danger">{{ $countOnHold }}</h3>
        <small class="text-muted">On Hold</small>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<form method="GET" action="{{ url('pages-tasks') }}" class="mb-3">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">
          <select class="form-select w-auto" name="status" onchange="this.form.submit()">
            <option value="All" {{ !$filterStatus || $filterStatus == 'All' ? 'selected' : '' }}>All Status</option>
            <option value="Completed" {{ $filterStatus == 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="In Progress" {{ $filterStatus == 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Not Started" {{ $filterStatus == 'Not Started' ? 'selected' : '' }}>Not Started</option>
            <option value="On Hold" {{ $filterStatus == 'On Hold' ? 'selected' : '' }}>On Hold</option>
          </select>
          <select class="form-select w-auto" name="brand" onchange="this.form.submit()">
            <option value="All" {{ !$filterBrand || $filterBrand == 'All' ? 'selected' : '' }}>All Brands</option>
            <option value="Supernata" {{ $filterBrand == 'Supernata' ? 'selected' : '' }}>Supernata</option>
            <option value="Dekornata" {{ $filterBrand == 'Dekornata' ? 'selected' : '' }}>Dekornata</option>
            <option value="Craftnata" {{ $filterBrand == 'Craftnata' ? 'selected' : '' }}>Craftnata</option>
          </select>
          <select class="form-select w-auto" name="category" onchange="this.form.submit()">
            <option value="All" {{ !$filterCategory || $filterCategory == 'All' ? 'selected' : '' }}>All Categories</option>
            <option value="Graphic Design" {{ $filterCategory == 'Graphic Design' ? 'selected' : '' }}>Graphic Design</option>
            <option value="3D Modelling" {{ $filterCategory == '3D Modelling' ? 'selected' : '' }}>3D Modelling</option>
            <option value="Content Writing" {{ $filterCategory == 'Content Writing' ? 'selected' : '' }}>Content Writing</option>
          </select>
          <div class="flex-grow-1">
            <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Search tasks..." onkeydown="if(event.key==='Enter'){this.form.submit();}">
          </div>
          <button type="submit" class="btn btn-outline-secondary">Filter</button>
          @if($filterStatus || $filterBrand || $filterCategory || $search)
            <a href="{{ url('pages-tasks') }}" class="btn btn-link">Reset</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</form>

<!-- Task List -->
<div class="row">
  <div class="col-12">
    @if($tasks->count() > 0)
      @foreach($tasks as $task)
        @php
          $borderColor = $statusColors[strtolower($task['status'])] ?? '#ccc';
        @endphp
        <a href="{{ url('tasks/'.$task['id']) }}" class="text-decoration-none">
          <div class="card mb-3" style="border-left:4px solid {{ $borderColor }}; cursor:pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
            <div class="card-body d-flex justify-content-between align-items-start">
              <div style="max-width:78%;">
                <h6 class="mb-1 text-body">{{ $task['title'] }}</h6>
                <p class="text-muted mb-2">{{ Str::limit($task['description'], 80) }}</p>
                <div class="d-flex flex-wrap gap-1">
                  <span class="badge 
                    @if($task['status']=='Completed') bg-success
                    @elseif($task['status']=='In Progress') bg-warning text-dark
                    @elseif($task['status']=='Pending') bg-primary
                    @else bg-danger @endif
                  ">{{ $task['status'] }}</span>
                  <span class="badge bg-danger">{{ $task['priority'] }}</span>
                  <span class="badge bg-primary">{{ $task['brand'] }}</span>
                  <span class="badge bg-secondary">{{ $task['category'] }}</span>
                </div>
              </div>
              <div class="text-end text-muted" style="min-width:140px;">
                <div class="mb-2"><i class="ti ti-user me-1"></i> {{ $task['assigned_to'] }}</div>
                <div><i class="ti ti-calendar me-1"></i> {{ $task['due_date'] }}</div>
              </div>
            </div>
          </div>
        </a>
      @endforeach

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
          Showing <strong>{{ ($page - 1) * $perPage + 1 }}-{{ min($page * $perPage, $total) }}</strong> of <strong>{{ $total }}</strong> tasks
        </div>
        <nav aria-label="Task pagination">
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
              <a class="page-link" href="?page={{ $page - 1 }}&status={{ $filterStatus }}&brand={{ $filterBrand }}&category={{ $filterCategory }}&search={{ $search }}" aria-label="Previous">
                <i class="ti ti-chevron-left ti-xs"></i>
              </a>
            </li>
            @for($i = 1; $i <= $lastPage; $i++)
              <li class="page-item {{ $page == $i ? 'active' : '' }}">
                <a class="page-link" href="?page={{ $i }}&status={{ $filterStatus }}&brand={{ $filterBrand }}&category={{ $filterCategory }}&search={{ $search }}">{{ $i }}</a>
              </li>
            @endfor
            <li class="page-item {{ $page == $lastPage ? 'disabled' : '' }}">
              <a class="page-link" href="?page={{ $page + 1 }}&status={{ $filterStatus }}&brand={{ $filterBrand }}&category={{ $filterCategory }}&search={{ $search }}" aria-label="Next">
                <i class="ti ti-chevron-right ti-xs"></i>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    @else
      <div class="text-center py-5 text-muted">
        <i class="ti ti-file-off ti-lg mb-2 d-block"></i>
        <p>No tasks found</p>
      </div>
    @endif
  </div>
</div>
@endsection