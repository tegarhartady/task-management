@php
$configData = Helper::appClasses();

// Define tab configuration
$tabs = [
    'all' => ['label' => 'All', 'icon' => null],
    'drafts' => ['label' => 'Drafts', 'icon' => 'ti-file'],
    'draft_revision' => ['label' => 'Draft Revision', 'icon' => 'ti-edit'],
    'new' => ['label' => 'New', 'icon' => 'ti-file-plus'],
    'assigned' => ['label' => 'Assigned', 'icon' => 'ti-user-check'],
    'submitted' => ['label' => 'Submitted', 'icon' => 'ti-send'],
    'revision' => ['label' => 'Revision', 'icon' => 'ti-refresh'],
    'approved' => ['label' => 'Approved', 'icon' => 'ti-check'],
];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Content')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Page Header with Filled Tabs -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <ul class="nav nav-pills" id="mainTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="planner-tab" data-bs-toggle="pill" data-bs-target="#planner" type="button" role="tab" aria-controls="planner" aria-selected="true">
          <i class="ti ti-file-text me-1"></i> Planner
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="performance-tab" data-bs-toggle="pill" data-bs-target="#performance" type="button" role="tab" aria-controls="performance" aria-selected="false">
          <i class="ti ti-chart-bar me-1"></i> Performance
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="content-list-tab" data-bs-toggle="pill" data-bs-target="#content-list" type="button" role="tab" aria-controls="content-list" aria-selected="false">
          <i class="ti ti-list me-1"></i> Content List
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="paid-ads-tab" data-bs-toggle="pill" data-bs-target="#paid-ads" type="button" role="tab" aria-controls="paid-ads" aria-selected="false">
          <i class="ti ti-ad me-1"></i> Paid Ads
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="content-bank-tab" data-bs-toggle="pill" data-bs-target="#content-bank" type="button" role="tab" aria-controls="content-bank" aria-selected="false">
          <i class="ti ti-archive me-1"></i> Content Bank
        </button>
      </li>
    </ul>
    <div class="d-flex gap-2">
      <a href="{{ route('pages-content.create') }}" class="btn btn-primary">
        <i class="ti ti-robot me-1"></i> AI Brief
      </a>
      <a href="{{ route('pages-content.create') }}" class="btn btn-outline-primary">
        <i class="ti ti-plus me-1"></i> New Brief
      </a>
    </div>
  </div>

  <!-- Tab Content -->
  <div class="tab-content" id="mainTabsContent">
    <!-- Planner Tab -->
    <div class="tab-pane fade show active" id="planner" role="tabpanel" aria-labelledby="planner-tab">
      <!-- Briefs Section -->
      <div class="card mb-4">
        <div class="card-header">
          <span class="badge bg-dark me-2"><i class="ti ti-file-description me-1"></i> Briefs</span>
        </div>
        <div class="card-body">
          <h5 class="mb-4">Content Briefs</h5>

          <!-- Status Filled Tabs -->
          <ul class="nav nav-pills flex-wrap gap-2 mb-4" id="briefStatusTabs" role="tablist">
            @foreach($tabs as $key => $tab)
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $key === 'all' ? 'active' : '' }}" id="{{ $key }}-tab" data-bs-toggle="pill" data-bs-target="#{{ $key }}-briefs" type="button" role="tab" aria-controls="{{ $key }}-briefs" aria-selected="{{ $key === 'all' ? 'true' : 'false' }}">
                @if($tab['icon'])<i class="ti {{ $tab['icon'] }} me-1"></i>@endif
                {{ $tab['label'] }}
                <span class="badge {{ $key === 'all' ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ $briefsData[$key]['total'] }}</span>
              </button>
            </li>
            @endforeach
          </ul>

          <!-- Filters -->
          <div class="d-flex gap-2 mb-4">
            <select class="form-select w-auto">
              <option>All Brands</option>
              <option>Dekornata</option>
              <option>Supernata</option>
              <option>Craftnata</option>
            </select>
            <select class="form-select w-auto">
              <option>All Briefs</option>
              <option>IGR</option>
              <option>IGS</option>
              <option>IGC</option>
            </select>
          </div>

          <!-- Brief Status Tab Content -->
          <div class="tab-content" id="briefStatusTabsContent">
            @foreach($tabs as $key => $tab)
            <div class="tab-pane fade {{ $key === 'all' ? 'show active' : '' }}" id="{{ $key }}-briefs" role="tabpanel" aria-labelledby="{{ $key }}-tab">
              @if(count($briefsData[$key]['items']) > 0)
                @foreach($briefsData[$key]['items'] as $item)
                <a href="{{ route('pages-content.show', $item['id']) }}" class="text-decoration-none">
                  <div class="card mb-3" style="border-left: 4px solid {{ $item['border_color'] }}; cursor: pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
                    <div class="card-body d-flex justify-content-between align-items-center">
                      <div>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                          <span class="badge bg-{{ $item['brand_color'] }}">{{ $item['brand'] }}</span>
                          @if($item['type'])
                          <span class="badge bg-secondary">{{ $item['type'] }}</span>
                          @endif
                          <span class="badge bg-{{ $item['status_color'] }} {{ $item['status_color'] === 'warning' ? 'text-dark' : '' }}">{{ $item['status'] }}</span>
                          @if($item['is_ai'])
                          <span class="badge bg-success"><i class="ti ti-robot me-1"></i> AI</span>
                          @endif
                        </div>
                        <h6 class="mb-1 text-body">{{ $item['title'] }}</h6>
                        <small class="text-muted">
                          By {{ $item['author'] }} • {{ $item['date'] }}
                          @if($item['comments'] > 0)
                          • <i class="ti ti-message me-1"></i> {{ $item['comments'] }}
                          @endif
                        </small>
                      </div>
                      <i class="ti ti-chevron-right text-muted"></i>
                    </div>
                  </div>
                </a>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                  <div class="text-muted">
                    Showing <strong>1-{{ count($briefsData[$key]['items']) }}</strong> of <strong>{{ $briefsData[$key]['total'] }}</strong> briefs
                  </div>
                  <nav aria-label="Brief pagination">
                    <ul class="pagination pagination-sm mb-0">
                      <li class="page-item disabled">
                        <a class="page-link" href="javascript:void(0);" aria-label="Previous">
                          <i class="ti ti-chevron-left ti-xs"></i>
                        </a>
                      </li>
                      <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">1</a>
                      </li>
                      @if($briefsData[$key]['total'] > 5)
                      <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">2</a>
                      </li>
                      <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">3</a>
                      </li>
                      <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">...</a>
                      </li>
                      <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">{{ ceil($briefsData[$key]['total'] / 5) }}</a>
                      </li>
                      @endif
                      <li class="page-item">
                        <a class="page-link" href="javascript:void(0);" aria-label="Next">
                          <i class="ti ti-chevron-right ti-xs"></i>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
              @else
                <div class="text-center py-5 text-muted">
                  <i class="ti {{ $tab['icon'] ?? 'ti-file' }} ti-lg mb-2 d-block"></i>
                  <p>No {{ strtolower($tab['label']) }} briefs found</p>
                </div>
              @endif
            </div>
            @endforeach
          </div>

        </div>
      </div>
    </div>

    <!-- Performance Tab -->
    <div class="tab-pane fade" id="performance" role="tabpanel" aria-labelledby="performance-tab">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="ti ti-chart-bar ti-lg mb-2 text-muted"></i>
          <h5>Performance</h5>
          <p class="text-muted">Performance analytics and metrics will be displayed here.</p>
        </div>
      </div>
    </div>

    <!-- Content List Tab -->
    <div class="tab-pane fade" id="content-list" role="tabpanel" aria-labelledby="content-list-tab">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="ti ti-list ti-lg mb-2 text-muted"></i>
          <h5>Content List</h5>
          <p class="text-muted">All content items will be displayed here.</p>
        </div>
      </div>
    </div>

    <!-- Paid Ads Tab -->
    <div class="tab-pane fade" id="paid-ads" role="tabpanel" aria-labelledby="paid-ads-tab">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="ti ti-ad ti-lg mb-2 text-muted"></i>
          <h5>Paid Ads</h5>
          <p class="text-muted">Paid advertising campaigns will be displayed here.</p>
        </div>
      </div>
    </div>

    <!-- Content Bank Tab -->
    <div class="tab-pane fade" id="content-bank" role="tabpanel" aria-labelledby="content-bank-tab">
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="ti ti-archive ti-lg mb-2 text-muted"></i>
          <h5>Content Bank</h5>
          <p class="text-muted">Saved content and templates will be displayed here.</p>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
