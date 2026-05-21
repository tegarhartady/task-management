<?php

namespace App\Http\Controllers\pages\performance;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;

class MyPerformance extends Controller
{
  public function index(Request $request)
  {
    $user = auth()->user();
    $timeRange = $request->get('range', '7d');
    $dateFrom = $request->get('from');
    $dateTo = $request->get('to');

    // If supervisor, admin, or manager, show list of employees
    if (in_array($user->role, ['supervisor', 'admin', 'manager', 'superadmin'])) {
      // Get employee performance data with pagination
      $employees = User::where('role', 'karyawan')
        ->where('is_active', true)
        ->paginate(5);

      // Map performance data to the paginated collection
      $mappedCollection = $employees->getCollection()->map(function ($employee) use ($timeRange, $dateFrom, $dateTo) {
        $performance = $this->calculateUserPerformance($employee, $timeRange, $dateFrom, $dateTo);
        return array_merge($performance, [
          'id' => $employee->id,
          'name' => $employee->name,
          'email' => $employee->email,
        ]);
      })->sortByDesc('score')->values();

      $employees->setCollection($mappedCollection);

      return view('content.pages.pages-performance', [
        'isSupervisor' => true,
        'employees' => $employees,
        'timeRange' => $timeRange,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
      ]);
    }

    // If karyawan, show own performance detail
    $performance = $this->calculateUserPerformance($user, $timeRange, $dateFrom, $dateTo);

    return view('content.pages.pages-performance', [
      'isSupervisor' => false,
      'userPerformance' => $performance,
      'timeRange' => $timeRange,
      'dateFrom' => $dateFrom,
      'dateTo' => $dateTo,
    ]);
  }

  public function show($userId)
  {
    $user = auth()->user();

    // Only supervisor and admin/manager can view employee performance
    if (!in_array($user->role, ['supervisor', 'admin', 'manager'])) {
      abort(403, 'Unauthorized');
    }

    $employee = User::findOrFail($userId);
    $timeRange = request()->get('range', '7d');
    $dateFrom = request()->get('from');
    $dateTo = request()->get('to');

    $performance = $this->calculateUserPerformance($employee, $timeRange, $dateFrom, $dateTo);

    return view('content.pages.pages-performance-detail', [
      'employee' => $employee,
      'performance' => $performance,
      'timeRange' => $timeRange,
      'dateFrom' => $dateFrom,
      'dateTo' => $dateTo,
    ]);
  }

  private function calculateUserPerformance($user, $timeRange, $dateFrom, $dateTo)
  {
    $query = Task::where('assigned_to', $user->id);

    // Apply date filters
    if ($dateFrom && $dateTo) {
      $query->whereBetween('created_at', [$dateFrom, $dateTo]);
    } elseif ($timeRange) {
      switch ($timeRange) {
        case '7d':
          $query->where('created_at', '>=', now()->subDays(7));
          break;
        case '30d':
          $query->where('created_at', '>=', now()->subDays(30));
          break;
        case '90d':
          $query->where('created_at', '>=', now()->subDays(90));
          break;
      }
    }

    $allTasks = $query->get();

    // Calculate performance metrics
    $completed = $allTasks->where('status', 'Completed')->count();
    $inProgress = $allTasks->where('status', 'In Progress')->count();
    $pending = $allTasks->where('status', 'Not Started')->count();
    $total = $allTasks->count();

    // Calculate score (0-100)
    $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;
    $score = intval($completionRate * 0.7 + (100 - intval($pending / max($total, 1)) * 100) * 0.3);

    // Determine grade
    $grade = match (true) {
      $score >= 90 => 'A',
      $score >= 80 => 'B',
      $score >= 70 => 'C',
      $score >= 65 => 'D',
      default => 'F',
    };

    // Calculate hours (based on priority: Low=1hr, Medium=3hrs, High=8hrs)
    $difficultyMap = [
      'Low' => 1,
      'Medium' => 3,
      'High' => 8
    ];
    $completedHours = $allTasks->where('status', 'Completed')->sum(function ($task) use ($difficultyMap) {
      return $difficultyMap[$task->priority] ?? 0;
    });
    $inProgressHours = $allTasks->where('status', 'In Progress')->sum(function ($task) use ($difficultyMap) {
      return $difficultyMap[$task->priority] ?? 0;
    });
    $assignedHours = $allTasks->sum(function ($task) use ($difficultyMap) {
      return $difficultyMap[$task->priority] ?? 0;
    });

    // Calculate weighted progress (utilization of effort)
    $totalWeight = $allTasks->sum(function ($task) use ($difficultyMap) {
      return $difficultyMap[$task->priority] ?? 1;
    });
    
    $weightedProgress = $allTasks->sum(function ($task) use ($difficultyMap) {
      $weight = $difficultyMap[$task->priority] ?? 1;
      return ($task->progress / 100) * $weight;
    });
    
    $availableHours = 160 - $assignedHours; 
    $utilization = $totalWeight > 0 ? ($weightedProgress / $totalWeight) * 100 : 0;

    // Prepare chart data (last 10 days)
    $chartData = [];
    for ($i = 9; $i >= 0; $i--) {
      $date = now()->subDays($i)->toDateString();
      $formattedDate = now()->subDays($i)->format('d/m');
      
      $dayTasks = $allTasks->filter(function($t) use ($date) {
        return $t->created_at->toDateString() === $date;
      });

      $chartData['labels'][] = $formattedDate;
      $chartData['completed'][] = $dayTasks->where('status', 'Completed')->count();
      $chartData['inProgress'][] = $dayTasks->where('status', 'In Progress')->count();
      $chartData['pending'][] = $dayTasks->where('status', 'Not Started')->count();
    }

    return [
      'user' => $user,
      'score' => $score,
      'grade' => $grade,
      'completed' => $completed,
      'inProgress' => $inProgress,
      'pending' => $pending,
      'total' => $total,
      'completedHours' => $completedHours,
      'inProgressHours' => $inProgressHours,
      'assignedHours' => $assignedHours,
      'availableHours' => max(0, $availableHours),
      'utilization' => round($utilization, 1),
      'tasks' => (clone $query)->latest()->paginate(5),
      'chartData' => $chartData
    ];
  }

  private function calculateEmployeesPerformance($employees, $timeRange, $dateFrom, $dateTo)
  {
    return $employees
      ->map(function ($employee) use ($timeRange, $dateFrom, $dateTo) {
        $performance = $this->calculateUserPerformance($employee, $timeRange, $dateFrom, $dateTo);
        return array_merge($performance, [
          'id' => $employee->id,
          'name' => $employee->name,
          'email' => $employee->email,
        ]);
      })
      ->sortByDesc('score')
      ->values();
  }
}
