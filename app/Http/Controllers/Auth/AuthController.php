<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  /**
   * Show login form
   */
  public function showLogin()
  {
    if (Auth::check()) {
      return redirect()->route('dashboard');
    }
    return view('auth.login');
  }

  /**
   * Handle login request
   */
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return back()
        ->with('error', 'Email atau password salah')
        ->withInput();
    }

    if (!$user->is_active) {
      return back()
        ->with('error', 'Akun Anda tidak aktif')
        ->withInput();
    }

    Auth::login($user, $request->remember);

    return redirect()->route('dashboard');
  }

  /**
   * Show register form
   */
  public function showRegister()
  {
    return view('auth.register');
  }

  /**
   * Handle register request
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|min:3',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6|confirmed',
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => 'karyawan',
      'is_active' => true,
    ]);

    return redirect()
      ->route('login')
      ->with('success', 'Registrasi berhasil, silakan login');
  }

  /**
   * Handle logout
   */
  public function logout()
  {
    Auth::logout();
    return redirect()
      ->route('login')
      ->with('success', 'Anda berhasil logout');
  }
}
