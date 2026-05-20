<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $query = User::query();

    if ($request->filled('search')) {
      $query->where(function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%')
          ->orWhere('email', 'like', '%' . $request->search . '%');
      });
    }

    if ($request->filled('role')) {
      $query->where('role', $request->role);
    }

    $users = $query->latest()->paginate(10);
    return view('content.pages.pages-users', compact('users'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'role' => ['required', 'in:superadmin,admin,supervisor,manager,karyawan'],
    ]);

    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => $request->role,
      'is_active' => true,
    ]);

    return redirect()->back()->with('success', 'User created successfully.');
  }

  public function update(Request $request, User $user)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
      'role' => ['required', 'in:superadmin,admin,supervisor,manager,karyawan'],
      'is_active' => ['required', 'boolean'],
    ]);

    $data = [
      'name' => $request->name,
      'email' => $request->email,
      'role' => $request->role,
      'is_active' => $request->is_active,
    ];

    if ($request->filled('password')) {
      $request->validate([
        'password' => ['confirmed', Rules\Password::defaults()],
      ]);
      $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->back()->with('success', 'User updated successfully.');
  }

  public function destroy(User $user)
  {
    if (auth()->user()->id === $user->id) {
      return redirect()->back()->with('error', 'You cannot delete yourself.');
    }

    if ($user->isSuperadmin() && !auth()->user()->isSuperadmin()) {
      return redirect()->back()->with('error', 'Only superadmins can delete other superadmins.');
    }

    $user->delete();
    return redirect()->back()->with('success', 'User deleted successfully.');
  }

  public function toggleStatus(User $user)
  {
    if (auth()->id() === $user->id) {
      return response()->json(['success' => false, 'error' => 'You cannot deactivate yourself.']);
    }

    $user->update(['is_active' => !$user->is_active]);
    return response()->json(['success' => true]);
  }
}
