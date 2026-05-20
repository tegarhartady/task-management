@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'User Performance')

@section('content')
<div class="container-fluid px-0">

  @if($isSupervisor)
    <!-- SUPERVISOR VIEW: Employee List -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Team Performance</h4>
      <button class="btn btn-outline-primary"><i class="ti ti-info-circle me-1"></i> How Grading Works</button>
    </div>

    <!-- Filters -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
      <div class="btn-group" role="group">
        <a href="?range=7d" class="btn btn-outline-primary {{ $timeRange === '7d' ? 'active' : '' }}">7d</a>
        <a href="?range=30d" class="btn btn-outline-primary {{ $timeRange === '30d' ? 'active' : '' }}">30d</a>
        <a href="?range=90d" class="btn btn-outline-primary {{ $timeRange === '90d' ? 'active' : '' }}">90d</a>
        <a href="?range=all" class="btn btn-outline-primary {{ !$timeRange || $timeRange === 'all' ? 'active' : '' }}">All</a>
      </div>
    </div>

    <!-- Team Stats -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <div class="text-muted mb-1">Active Employees</div>
            <h3 class="mb-0">{{ count($employees) }}</h3>
            <small class="text-muted">With assigned tasks</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <div class="text-muted mb-1">Avg Team Score</div>
            @php
              $scores = collect($employees)->pluck('score')->toArray();
              $avgScore = count($employees) > 0 ? intval(array_sum($scores) / count($employees)) : 0;
              $avgGrade = match (true) {
                $avgScore >= 90 => 'A',
                $avgScore >= 80 => 'B',
                $avgScore >= 70 => 'C',
                $avgScore >= 65 => 'D',
                default => 'F'
              };
              $gradeColor = match ($avgGrade) {
                'A' => 'success',
                'B' => 'info',
                'C' => 'warning',
                'D' => 'danger',
                'F' => 'danger',
              };
            @endphp
            <h3 class="mb-0">{{ $avgScore }}</h3>
            <span class="badge bg-{{ $gradeColor }}">Grade {{ $avgGrade }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-success">
          <div class="card-body">
            <div class="text-muted mb-1">Top Performer</div>
            @php
              $topPerformer = $employees[0] ?? null;
            @endphp
            @if($topPerformer)
              <h3 class="mb-0 text-success">{{ $topPerformer['name'] }}</h3>
              <small>Score: {{ $topPerformer['score'] }} ({{ $topPerformer['grade'] }})</small>
            @else
              <h3 class="mb-0 text-muted">-</h3>
            @endif
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-danger">
          <div class="card-body">
            <div class="text-muted mb-1">Needs Attention</div>
            @php
              $needsAttention = collect($employees)->where('score', '<', 65)->count();
            @endphp
            <h3 class="mb-0 text-danger">{{ $needsAttention }}</h3>
            <small>Score below 65</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Employee Performance List -->
    <div class="row g-3">
      @forelse($employees as $emp)
        @php
          $gradeColors = [
            'A' => '#28c76f',
            'B' => '#00cfe8',
            'C' => '#ff9f43',
            'D' => '#ea5455',
            'F' => '#ea5455',
          ];
          $sideColor = $gradeColors[$emp['grade']] ?? '#696cff';
        @endphp
        <div class="col-12">
          <div class="card shadow-sm mb-3" style="border-left: 5px solid {{ $sideColor }};">
            <div class="card-body">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3" style="min-width: 250px;">
                  <div class="avatar avatar-md">
                    <span class="avatar-initial rounded-circle bg-label-primary fw-bold">{{ strtoupper(substr($emp['name'], 0, 1)) }}</span>
                  </div>
                  <div>
                    <h6 class="mb-0 fw-bold">{{ $emp['name'] }}</h6>
                    <small class="text-muted"><i class="ti ti-mail me-1"></i>{{ $emp['email'] }}</small>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-4 flex-grow-1 justify-content-center">
                  <div class="text-center">
                    <h5 class="mb-0 fw-bold">{{ $emp['score'] }}</h5>
                    <small class="text-muted">Score</small>
                  </div>
                  <div class="text-center">
                    <span class="badge" style="background-color: {{ $sideColor }};">Grade {{ $emp['grade'] }}</span>
                    <small class="text-muted d-block mt-1">Ranking</small>
                  </div>
                  <div class="text-center d-none d-md-block">
                    <div class="d-flex gap-1 mb-1">
                      <span class="badge bg-label-success">{{ $emp['completed'] }}</span>
                      <span class="badge bg-label-info">{{ $emp['inProgress'] }}</span>
                      <span class="badge bg-label-secondary">{{ $emp['pending'] }}</span>
                    </div>
                    <small class="text-muted">Tasks (S/P/B)</small>
                  </div>
                </div>

                <div class="text-end d-flex gap-2">
                  <a href="{{ route('pages-performance.show', $emp['id']) }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-chart-pie me-1"></i> Report
                  </a>
                  <!-- <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignTaskModal-{{ $emp['id'] }}">
                    <i class="ti ti-plus me-1"></i> Task
                  </button> -->
                </div>
              </div>

              <!-- Progress Bar for Utilization -->
              <div class="mt-3">
                <div class="d-flex justify-content-between mb-1">
                  <small class="text-muted">Resource Utilization</small>
                  <small class="fw-bold">{{ $emp['utilization'] }}%</small>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar" role="progressbar" style="width: {{ $emp['utilization'] }}%; background-color: {{ $sideColor }};" aria-valuenow="{{ $emp['utilization'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center py-5 text-muted">
              <i class="ti ti-users-off" style="font-size: 48px; opacity: 0.3;"></i>
              <p class="mt-3">No employees found in this criteria.</p>
            </div>
          </div>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      {{ $employees->links() }}
    </div>

  @else
    <!-- KARYAWAN VIEW: Own Performance Detail -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">My Performance</h4>
      <button class="btn btn-outline-primary"><i class="ti ti-info-circle me-1"></i> How Grading Works</button>
    </div>

    <!-- Filters -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
      <div class="btn-group" role="group">
        <a href="?range=7d" class="btn btn-outline-primary {{ $timeRange === '7d' ? 'active' : '' }}">7d</a>
        <a href="?range=30d" class="btn btn-outline-primary {{ $timeRange === '30d' ? 'active' : '' }}">30d</a>
        <a href="?range=90d" class="btn btn-outline-primary {{ $timeRange === '90d' ? 'active' : '' }}">90d</a>
        <a href="?range=all" class="btn btn-outline-primary {{ !$timeRange || $timeRange === 'all' ? 'active' : '' }}">All</a>
      </div>
    </div>

    <!-- Performance Overview -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card h-100 text-center p-4 d-flex flex-column align-items-center justify-content-center">
          <div class="mb-3">{{ auth()->user()->name }} <span class="badge bg-{{ $gradeColors[$userPerformance['grade']] ?? 'secondary' }} text-white">{{ $userPerformance['grade'] }}</span></div>

          @php
            $gradeColors = [
              'A' => 'success',
              'B' => 'info',
              'C' => 'warning',
              'D' => 'danger',
              'F' => 'danger',
            ];
            $scoreColor = match ($userPerformance['grade']) {
              'A' => '#198754',
              'B' => '#0dcaf0',
              'C' => '#ffc107',
              'D' => '#dc3545',
              'F' => '#dc3545',
            };
            $circumference = 2 * 3.14159 * 45;
            $offset = $circumference - ($userPerformance['score'] / 100) * $circumference;
          @endphp

          <div class="position-relative d-inline-block mb-3" style="width:120px;height:120px;">
            <svg width="120" height="120">
              <circle cx="60" cy="60" r="45" fill="none" stroke="#eee" stroke-width="10"/>
              <circle cx="60" cy="60" r="45" fill="none" stroke="{{ $scoreColor }}" stroke-width="10" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" stroke-linecap="round"/>
            </svg>
            <div class="position-absolute top-50 start-50 translate-middle text-center">
              <span class="fs-3 fw-bold">{{ $userPerformance['score'] }}</span>
            </div>
          </div>

          <div class="mb-2">#1 <span class="text-muted">rank</span></div>
          <div class="mb-2">{{ number_format(($userPerformance['completed'] / max($userPerformance['total'], 1)) * 100, 0) }}% <span class="text-muted">complete</span></div>
          <div>quality</div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="card h-100 p-4">
          <div class="mb-3 fw-bold">Time Utilization <span class="text-muted fw-normal" style="font-size:13px;">Based on 40hr/week • Hours estimated from difficulty</span></div>
          <div class="mb-3" style="font-size:13px;">
            Difficulty: 1=1hr, 2=3hrs, 3=8hrs, 4=12hrs, 5=20hrs+. Shared tasks split hours among assignees.
          </div>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Status</th>
                  <th>COMPLETED HRS</th>
                  <th>IN PROGRESS HRS</th>
                  <th>ASSIGNED HRS</th>
                  <th>AVAILABLE HRS</th>
                  <th>UTILIZATION</th>
                  <th>BREAKDOWN</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>{{ auth()->user()->name }}</strong></td>
                  <td>{{ $userPerformance['completedHours'] }}h</td>
                  <td>{{ $userPerformance['inProgressHours'] }}h</td>
                  <td>{{ $userPerformance['assignedHours'] }}h</td>
                  <td>{{ $userPerformance['availableHours'] }}h</td>
                  <td>
                    @php
                      $utilizationBadgeColor = match (true) {
                        $userPerformance['utilization'] >= 80 => 'success',
                        $userPerformance['utilization'] >= 60 => 'info',
                        $userPerformance['utilization'] >= 40 => 'warning',
                        default => 'danger',
                      };
                    @endphp
                    <span class="badge bg-{{ $utilizationBadgeColor }}">{{ number_format($userPerformance['utilization'], 1) }}%</span>
                  </td>
                  <td style="min-width:200px;">
                    <div class="d-flex align-items-center gap-1">
                      <div style="width:20px;height:12px;border-radius:3px;background-color:#198754;"></div>
                      <span style="font-size:11px;">Completed</span>
                      <div style="width:20px;height:12px;border-radius:3px;background-color:#0dcaf0;margin-left:8px;"></div>
                      <span style="font-size:11px;">In Progress</span>
                      <div style="width:20px;height:12px;border-radius:3px;background-color:#e9ecef;margin-left:8px;"></div>
                      <span style="font-size:11px;">Available</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>


    <!-- Task Summary -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <div class="text-muted mb-2">Total Tasks</div>
            <h3 class="mb-0">{{ $userPerformance['total'] }}</h3>
            <small class="text-muted">Assigned tasks</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-success">
          <div class="card-body">
            <div class="text-muted mb-2">Completed</div>
            <h3 class="mb-0 text-success">{{ $userPerformance['completed'] }}</h3>
            <small class="text-success">{{ number_format(($userPerformance['completed'] / max($userPerformance['total'], 1)) * 100, 1) }}% completion</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-info">
          <div class="card-body">
            <div class="text-muted mb-2">In Progress</div>
            <h3 class="mb-0 text-info">{{ $userPerformance['inProgress'] }}</h3>
            <small class="text-info">Currently working</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100 border-secondary">
          <div class="card-body">
            <div class="text-muted mb-2">Pending</div>
            <h3 class="mb-0 text-secondary">{{ $userPerformance['pending'] }}</h3>
            <small class="text-secondary">Not started</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Performance Chart -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold">Task Analytics Trend</h5>
            <div class="d-flex align-items-center gap-3">
              <span class="badge badge-dot bg-success me-1"></span> <small class="text-muted me-3">Completed</small>
              <span class="badge badge-dot bg-warning me-1"></span> <small class="text-muted me-3">In Progress</small>
              <span class="badge badge-dot bg-secondary me-1"></span> <small class="text-muted">Pending</small>
            </div>
          </div>
          <div class="card-body p-0">
            <div id="performanceStackedChart" style="min-height: 300px;"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Details List -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 fw-bold">Recent Tasks Detail</h5>
    </div>

    <div class="row g-3">
      @forelse($userPerformance['tasks'] as $task)
        @php
          $statusColor = match($task->status) {
            'Completed' => 'success',
            'In Progress' => 'warning',
            'Pending Review' => 'primary',
            'Approved' => 'success',
            'Rejected' => 'danger',
            default => 'secondary'
          };
        @endphp
        <div class="col-12">
          <div class="card shadow-sm" style="border-left: 4px solid var(--bs-{{ $statusColor }});">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex gap-3">
                  <div class="avatar avatar-md bg-label-{{ $statusColor }} p-2">
                    <i class="ti ti-checklist fs-3"></i>
                  </div>
                  <div>
                    <h6 class="mb-1 fw-bold">{{ $task->title }}</h6>
                    <p class="text-muted small mb-2">{{ Str::limit($task->description, 100) }}</p>
                    <div class="d-flex gap-2">
                      <span class="badge bg-label-{{ $statusColor }}">{{ $task->status }}</span>
                      <span class="badge bg-label-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'info') }}">
                        {{ $task->priority }} Priority
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-end">
                  <div class="mb-2">
                    <small class="text-muted d-block">Due Date</small>
                    <strong class="{{ $task->isOverdue() ? 'text-danger' : '' }}">{{ $task->due_date?->format('d M Y') ?? 'No Deadline' }}</strong>
                  </div>
                  <div class="progress" style="height: 6px; width: 100px;">
                    <div class="progress-bar bg-{{ $statusColor }}" style="width: {{ $task->progress }}%"></div>
                  </div>
                  <small class="text-muted">{{ $task->progress }}% complete</small>
                </div>
              </div>
              <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Created by: {{ $task->creator->name }}</small>
                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-label-primary">View Details</a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center py-5">
          <p class="text-muted">No tasks found for this period.</p>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
      {{ $userPerformance['tasks']->appends(request()->query())->links() }}
    </div>
    @endif

</div>
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>
  (function() {
    let cardColor, labelColor, headingColor, borderColor;

    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    borderColor = config.colors.borderColor;

    const performanceChartEl = document.querySelector('#performanceStackedChart'),
      performanceChartConfig = {
        series: [{
            name: 'Completed',
            data: {!! json_encode($isSupervisor ? [] : $userPerformance['chartData']['completed']) !!}
          },
          {
            name: 'In Progress',
            data: {!! json_encode($isSupervisor ? [] : $userPerformance['chartData']['inProgress']) !!}
          },
          {
            name: 'Pending',
            data: {!! json_encode($isSupervisor ? [] : $userPerformance['chartData']['pending']) !!}
          }
        ],
        chart: {
          type: 'bar',
          height: 300,
          stacked: true,
          toolbar: {
            show: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '35%',
            borderRadius: 4,
            startingShape: 'rounded',
            endingShape: 'rounded'
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        grid: {
          borderColor: borderColor,
          padding: {
            top: -20,
            bottom: -10,
            left: 10,
            right: 10
          }
        },
        xaxis: {
          categories: {!! json_encode($isSupervisor ? [] : $userPerformance['chartData']['labels']) !!},
          labels: {
            style: {
              colors: labelColor,
              fontSize: '13px'
            }
          },
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          }
        },
        yaxis: {
          labels: {
            style: {
              colors: labelColor,
              fontSize: '13px'
            }
          }
        },
        fill: {
          opacity: 1
        },
        legend: {
          show: false
        },
        colors: [config.colors.success, config.colors.warning, config.colors.secondary],
        states: {
          hover: {
            filter: {
              type: 'none'
            }
          },
          active: {
            filter: {
              type: 'none'
            }
          }
        }
      };
    if (typeof performanceChartEl !== 'undefined' && performanceChartEl !== null) {
      const performanceChart = new ApexCharts(performanceChartEl, performanceChartConfig);
      performanceChart.render();
    }
  })();
</script>
@endsection
