<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
  public function run(): void
  {
    $admin = User::where('role', 'admin')->first();
    $creative = User::where('role', 'creative_director')->first();
    $finance = User::where('role', 'finance')->first();
    $timInternal = User::where('role', 'tim_internal')
      ->take(3)
      ->get();

    // Sample tasks
    $tasks = [
      [
        'title' => 'Fix Homepage Design',
        'description' => 'Update homepage with new UI/UX design',
        'priority' => 'High',
        'status' => 'In Progress',
        'created_by' => $admin->id,
        'assigned_to' => $timInternal[0]->id,
        'due_date' => now()->addDays(5),
        'progress' => 60,
      ],
      [
        'title' => 'Database Migration',
        'description' => 'Migrate from old database to new schema',
        'priority' => 'High',
        'status' => 'Pending Review',
        'created_by' => $admin->id,
        'assigned_to' => $creative->id,
        'reviewed_by' => $creative->id,
        'due_date' => now()->addDays(3),
        'progress' => 85,
      ],
      [
        'title' => 'API Documentation',
        'description' => 'Create comprehensive API documentation',
        'priority' => 'Medium',
        'status' => 'Not Started',
        'created_by' => $creative->id,
        'assigned_to' => $timInternal[1]->id,
        'due_date' => now()->addDays(7),
        'progress' => 0,
      ],
      [
        'title' => 'Performance Optimization',
        'description' => 'Optimize application performance',
        'priority' => 'Medium',
        'status' => 'In Progress',
        'created_by' => $admin->id,
        'assigned_to' => $timInternal[2]->id,
        'due_date' => now()->addDays(10),
        'progress' => 40,
      ],
      [
        'title' => 'Bug Fixes',
        'description' => 'Fix reported bugs from production',
        'priority' => 'Low',
        'status' => 'Completed',
        'created_by' => $creative->id,
        'assigned_to' => $timInternal[0]->id,
        'reviewed_by' => $creative->id,
        'due_date' => now()->subDays(2),
        'progress' => 100,
      ],
    ];

    foreach ($tasks as $taskData) {
      Task::create($taskData);
    }
  }
}
