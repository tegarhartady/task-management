<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  /**
   * Display a listing of users
   */
  public function index(Request $request)
  {
    $query = User::query();

    // Search by name or email
    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
      });
    }

    // Filter by role
    if ($request->filled('role')) {
      $query->where('role', $request->role);
    }

    $users = $query->latest()->paginate(5);
    return view('content.pages.pages-users', compact('users'));
  }

  /**
   * Show the form for creating a new user
   */
  public function create()
  {
    return view('content.pages.pages-users-create');
  }

  /**
   * Store a newly created user
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users',
      'password' => 'required|string|min:8|confirmed',
      'role' => 'required|in:admin,supervisor,manager,karyawan',
      'is_active' => 'boolean',
    ]);

    $validated['password'] = bcrypt($validated['password']);
    $validated['is_active'] = $request->has('is_active');

    User::create($validated);

    return redirect()
      ->route('users.index')
      ->with('success', 'User created successfully');
  }

  /**
   * Show the form for editing the specified user
   */
  public function edit(User $user)
  {
    return view('content.pages.pages-users-edit', compact('user'));
  }

  /**
   * Update the specified user
   */
  public function update(Request $request, User $user)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|email|unique:users,email,' . $user->id,
      'role' => 'required|in:admin,supervisor,manager,karyawan',
      'is_active' => 'boolean',
    ]);

    // Only update password if provided
    if ($request->filled('password')) {
      $request->validate(['password' => 'string|min:8|confirmed']);
      $validated['password'] = bcrypt($request->password);
    }

    $validated['is_active'] = $request->has('is_active');

    $user->update($validated);

    return redirect()
      ->route('users.index')
      ->with('success', 'User updated successfully');
  }

  /**
   * Delete the specified user
   */
  public function destroy(User $user)
  {
    // Prevent deleting self
    if ($user->id === auth()->id()) {
      return redirect()
        ->route('users.index')
        ->with('error', 'Cannot delete your own account');
    }

    $user->delete();

    return redirect()
      ->route('users.index')
      ->with('success', 'User deleted successfully');
  }

  /**
   * Toggle user active status
   */
  public function toggleStatus(User $user)
  {
    // Prevent deactivating self
    if ($user->id === auth()->id()) {
      return response()->json(['error' => 'Cannot deactivate your own account'], 403);
    }

    $user->update(['is_active' => !$user->is_active]);

    return response()->json([
      'success' => true,
      'message' => $user->is_active ? 'User activated' : 'User deactivated',
      'is_active' => $user->is_active,
    ]);
  }
}
