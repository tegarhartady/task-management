<?php

namespace App\Http\Controllers;

use App\Models\Brief;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;

class TaskController extends Controller
{
  public function index()
  {
    $user = auth()->user();

    $query = Task::with('creator', 'assignedTo', 'reviewedBy');

    // Filter by status
    if (request('status')) {
      $query->where('status', request('status'));
    }

    // Filter by priority
    if (request('priority')) {
      $query->where('priority', request('priority'));
    }

    // Search
    if (request('search')) {
      $search = request('search');
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
      });
    }
    
    // Get base query for stats (without status/priority filters)
    $statsQuery = Task::query();
    if ($user->isManager()) {
      $statsQuery->where(function ($q) use ($user) {
        $q->where('created_by', $user->id)->orWhere('assigned_to', $user->id);
      });
    } elseif (!$user->isAdmin() && !$user->isSupervisor()) {
      $statsQuery->where(function ($q) use ($user) {
        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
      });
    }

    $countAll = (clone $statsQuery)->count();
    $countCompleted = (clone $statsQuery)->where('status', 'Completed')->count();
    $countInProgress = (clone $statsQuery)->where('status', 'In Progress')->count();
    $countPending = (clone $statsQuery)->where('status', 'Pending Review')->count();
    $countApproved = (clone $statsQuery)->where('status', 'Approved')->count();
    $countOverdue = (clone $statsQuery)->whereNotIn('status', ['Completed', 'Approved'])
        ->whereNotNull('due_date')
        ->where('due_date', '<', now()->toDateString())
        ->count();

    // Get tasks based on user role (with filters)
    if ($user->isAdmin() || $user->isSupervisor()) {
      $tasks = $query->latest()->paginate(5);
    } elseif ($user->isManager()) {
      $tasks = $query->where(function ($q) use ($user) {
        $q->where('created_by', $user->id)->orWhere('assigned_to', $user->id);
      })->latest()->paginate(5);
    } else {
      $tasks = $query->where(function ($q) use ($user) {
        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
      })->latest()->paginate(5);
    }

    $myBriefs = \App\Models\Brief::where(function($q) use ($user) {
        $q->whereHas('assignees', function($q2) use ($user) {
            $q2->where('user_id', $user->id);
        })->orWhere('assigned_to', $user->id);
    })->where('status', '!=', 'Completed')->get();

    return view('content.tasks.index', compact('tasks', 'countAll', 'countCompleted', 'countInProgress', 'countPending', 'countApproved', 'countOverdue', 'myBriefs'));
  }

  public function show(Task $task)
  {
    $task->load('creator', 'assignedTo', 'reviewedBy', 'attachments.uploadedBy', 'comments.user');
    return view('content.tasks.show', compact('task'));
  }

  public function create()
  {
    $myBriefs = Brief::where(function($q) {
        $q->whereHas('assignees', function($q2) {
            $q2->where('user_id', auth()->id());
        })->orWhere('assigned_to', auth()->id());
    })->where('status', '!=', 'Completed')->get();

    return view('content.tasks.create', compact('myBriefs'));
  }

  public function edit(Task $task)
  {
    // Only task creator can edit
    if ($task->created_by !== auth()->id()) {
      abort(403, 'You cannot edit this task');
    }

    // Can edit if: Not Started, In Progress (untuk revisi setelah di-review)
    // Cannot edit if: Completed
    if ($task->status === 'Completed') {
      abort(403, 'Cannot edit completed tasks');
    }

    $task->load('attachments');
    return view('content.tasks.edit', compact('task'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'brief_id' => 'required|exists:briefs,id',
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'priority' => 'required|in:Low,Medium,High',
      'assigned_to' => 'nullable|exists:users,id',
      'due_date' => 'required|date',
    ]);

    $brief = Brief::findOrFail($request->brief_id);

    // Validasi deadline: Task due date cannot exceed Brief due date
    if ($brief->due_date && strtotime($request->due_date) > strtotime($brief->due_date)) {
        return back()->withErrors([
            'due_date' => 'Deadline task tidak boleh melewati batas waktu Brief (' . $brief->due_date->format('Y-m-d') . ').'
        ])->withInput();
    }

    $validated['created_by'] = auth()->id();
    $validated['status'] = 'Not Started';

    // Auto-assign to creator if they are karyawan (employee)
    if (
      auth()
        ->user()
        ->isKaryawan() &&
      empty($validated['assigned_to'])
    ) {
      $validated['assigned_to'] = auth()->id();
    }
    
    $task = Task::create($validated);

    if ($request->hasFile('attachments')) {
      foreach ($request->file('attachments') as $file) {
        $path = $file->store('task-attachments', 'public');
        TaskAttachment::create([
          'task_id' => $task->id,
          'type' => 'image',
          'file_path' => $path,
          'original_name' => $file->getClientOriginalName(),
          'uploaded_by' => auth()->id(),
        ]);
      }
    }

    if ($request->filled('links')) {
      foreach (explode(',', $request->links) as $link) {
        $link = trim($link);
        if ($link) {
          TaskAttachment::create([
            'task_id' => $task->id,
            'type' => 'link',
            'link' => $link,
            'uploaded_by' => auth()->id(),
          ]);
        }
      }
    }

    return redirect()
      ->route('tasks.show', $task)
      ->with('success', 'Task created successfully!');
  }

  public function update(Task $task, Request $request)
  {
    // Only task creator can update, and only if not completed/approved
    if ($task->created_by !== auth()->id()) {
      abort(403, 'You cannot edit this task');
    }

    if (in_array($task->status, ['Completed', 'Approved'])) {
      abort(403, 'Cannot edit completed or approved tasks');
    }

    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'priority' => 'required|in:Low,Medium,High',
      'assigned_to' => 'nullable|exists:users,id',
      'due_date' => 'nullable|date',
    ]);

    $task->update($validated);

    // Handle new attachments
    if ($request->hasFile('attachments')) {
      foreach ($request->file('attachments') as $file) {
        $path = $file->store('task-attachments', 'public');
        TaskAttachment::create([
          'task_id' => $task->id,
          'type' => 'image',
          'file_path' => $path,
          'original_name' => $file->getClientOriginalName(),
          'uploaded_by' => auth()->id(),
        ]);
      }
    }

    // Handle new links
    if ($request->filled('links')) {
      foreach (explode(',', $request->links) as $link) {
        $link = trim($link);
        if ($link) {
          TaskAttachment::create([
            'task_id' => $task->id,
            'type' => 'link',
            'link' => $link,
            'uploaded_by' => auth()->id(),
          ]);
        }
      }
    }

    // Handle deleted attachments
    if ($request->filled('deleted_attachments')) {
      $deletedIds = explode(',', $request->deleted_attachments);
      TaskAttachment::whereIn('id', $deletedIds)->delete();
    }

    return redirect()
      ->route('tasks.show', $task)
      ->with('success', 'Task updated successfully!');
  }

  public function checkIn(Task $task)
  {
    if (!$task->canCheckIn()) {
      return back()->with('error', 'Cannot check in to this task');
    }

    // Reset checked_out_at when checking in again (allow multiple cycles)
    $task->update([
      'checked_in_at' => now(),
      'checked_out_at' => null,
      'status' => 'In Progress',
    ]);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => auth()->user()->name . ' checked in',
      'type' => 'status_change',
    ]);

    return back()->with('success', 'Checked in successfully!');
  }

  public function checkOut(Task $task)
  {
    if (!$task->canCheckOut()) {
      return back()->with('error', 'Cannot check out from this task');
    }

    $task->update(['checked_out_at' => now()]);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => auth()->user()->name . ' checked out',
      'type' => 'status_change',
    ]);

    return back()->with('success', 'Checked out successfully!');
  }

  public function submitForReview(Task $task)
  {
    if ($task->status !== 'In Progress') {
      return back()->with('error', 'Task must be in progress to submit for review');
    }

    // Increment progress by 25% (0 -> 25 -> 50 -> 75 -> 100)
    $newProgress = min($task->progress + 25, 100);
    $task->update(['progress' => $newProgress]);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => auth()->user()->name . ' submitted task for review (Progress: ' . $newProgress . '%)',
      'type' => 'status_change',
    ]);

    return back()->with('success', 'Task submitted for review! Progress updated to ' . $newProgress . '%');
  }

  public function approve(Task $task, Request $request)
  {
    $request->validate(['comment' => 'required|string']);

    // Status tetap "In Progress", tapi progress bertambah
    $task->update(['reviewed_by' => auth()->id()]);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => $request->comment,
      'type' => 'approval',
    ]);

    return back()->with('success', 'Task approved!');
  }

  public function reject(Task $task, Request $request)
  {
    $request->validate(['comment' => 'required|string']);

    // Status tetap "In Progress", progress tidak berubah
    // Reset check-in/out times so karyawan can check-in again for revision
    // Karyawan bisa revisi dan submit lagi
    $task->update([
      'reviewed_by' => auth()->id(),
      'checked_in_at' => null,
      'checked_out_at' => null,
    ]);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => $request->comment,
      'type' => 'rejection',
    ]);

    return back()->with('success', 'Task rejected! Karyawan can revise and resubmit.');
  }

  public function addComment(Task $task, Request $request)
  {
    $request->validate(['comment' => 'required|string']);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => $request->comment,
      'type' => 'comment',
    ]);

    return back()->with('success', 'Comment added!');
  }

  public function markCompleted(Task $task)
  {
    // Only assigned user can mark as completed, when progress reaches 100%
    if ($task->assignedTo?->id !== auth()->id()) {
      abort(403, 'Only assigned user can mark this task as completed');
    }

    if ($task->status !== 'In Progress') {
      return back()->with('error', 'Task must be in progress');
    }

    if ($task->progress !== 100) {
      return back()->with('error', 'Task progress must be 100% to mark as completed');
    }

    $task->update(['status' => 'Completed']);

    TaskComment::create([
      'task_id' => $task->id,
      'user_id' => auth()->id(),
      'comment' => auth()->user()->name . ' marked task as completed',
      'type' => 'status_change',
    ]);

    return back()->with('success', 'Task marked as completed!');
  }
}
