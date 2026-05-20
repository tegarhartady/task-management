<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  /**
   * Show admin dashboard
   */
  public function adminDashboard()
  {
    $user = auth()->user();
    $data = [
      'title' => 'Admin Dashboard',
      'totalUsers' => \App\Models\User::count(),
      'totalAdmins' => \App\Models\User::where('role', 'admin')->count(),
      'totalSupervisors' => \App\Models\User::where('role', 'supervisor')->count(),
      'totalManagers' => \App\Models\User::where('role', 'manager')->count(),
      'totalKaryawan' => \App\Models\User::where('role', 'karyawan')->count(),
      'activeUsers' => \App\Models\User::where('is_active', true)->count(),
      'inactiveUsers' => \App\Models\User::where('is_active', false)->count(),
      'totalTasks' => \App\Models\Task::count(),
      'overdueTasks' => \App\Models\Task::whereNotIn('status', ['Completed', 'Approved'])
        ->whereNotNull('due_date')
        ->where('due_date', '<', now()->toDateString())
        ->count(),
    ];
    return view('content.dashboards.admin-dashboard', $data);
  }

  /**
   * Show supervisor dashboard
   */
  public function supervisorDashboard()
  {
    $user = auth()->user();

    // Get team members (karyawan)
    $teamMembers = \App\Models\User::where('role', 'karyawan')->get();

    // Get tasks data
    $tasks = \App\Models\Task::with('creator', 'assignedTo')->get();

    // Get briefs data
    $briefs = \App\Models\Brief::get();

    // Get reimbursement data
    $reimbursements = \App\Models\Reimbursement::get();

    // Calculate statistics
    $today = now()->format('Y-m-d');
    $todayTasks = $tasks
      ->filter(function ($task) use ($today) {
        return $task->due_date &&
          $task->due_date >= $today &&
          $task->due_date <
            now()
              ->addDay()
              ->format('Y-m-d');
      })
      ->count();

    $completedTasks = $tasks->where('status', 'Completed')->count();

    $overdueTasks = \App\Models\Task::whereNotIn('status', ['Completed', 'Approved'])
      ->whereNotNull('due_date')
      ->where('due_date', '<', now()->toDateString())
      ->count();

    return view(
      'content.dashboards.supervisor-dashboard',
      compact(
        'teamMembers',
        'tasks',
        'briefs',
        'reimbursements',
        'today',
        'todayTasks',
        'completedTasks',
        'overdueTasks'
      )
    );
  }

  /**
   * Show manager dashboard
   */
  public function managerDashboard()
  {
    $user = auth()->user();
    $data = [
      'title' => 'Manager Dashboard',
      'teamCount' => \App\Models\User::where('role', 'karyawan')->count(),
      'activeTeamMembers' => \App\Models\User::where('role', 'karyawan')
        ->where('is_active', true)
        ->count(),
      'overdueTasks' => \App\Models\Task::whereNotIn('status', ['Completed', 'Approved'])
        ->whereNotNull('due_date')
        ->where('due_date', '<', now()->toDateString())
        ->count(),
      'pendingReimburse' => \App\Models\Reimbursement::where('status', 'Pending')->sum('amount'),
      'totalReimburse' => \App\Models\Reimbursement::sum('amount'),
      'approvedReimburse' => \App\Models\Reimbursement::where('status', 'Approved')->sum('amount'),
    ];
    return view('content.dashboards.manager-dashboard', $data);
  }

  /**
   * Show karyawan dashboard
   */
  public function karyawanDashboard()
  {
    $user = auth()->user();
    
    // Real data from DB
    $myTasks = \App\Models\Task::where('assigned_to', $user->id)->get();
    $completedTasks = $myTasks->where('status', 'Completed')->count();
    $inProgressTasks = $myTasks->where('status', 'In Progress')->count();
    $overdueTasks = \App\Models\Task::where('assigned_to', $user->id)
        ->whereNotIn('status', ['Completed', 'Approved'])
        ->whereNotNull('due_date')
        ->where('due_date', '<', now()->toDateString())
        ->count();
    
    $myReimbursements = \App\Models\Reimbursement::where('submitted_by', $user->id)->get();
    $totalReimburse = $myReimbursements->sum('amount');
    $approvedReimburse = $myReimbursements->where('status', 'Approved')->sum('amount');
    
    $totalBriefs = \App\Models\Brief::count();

    $data = [
      'title' => 'Karyawan Dashboard',
      'userEmail' => $user->email,
      'completedTasks' => $completedTasks,
      'inProgressTasks' => $inProgressTasks,
      'overdueTasks' => $overdueTasks,
      'totalReimburse' => $totalReimburse,
      'approvedReimburse' => $approvedReimburse,
      'totalBriefs' => $totalBriefs,
      'myTasks' => $myTasks->sortByDesc('created_at')->take(5),
    ];
    return view('content.dashboards.karyawan-dashboard', $data);
  }
}
