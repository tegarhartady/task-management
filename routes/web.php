<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\pages\tasks\TaskPage;
use App\Http\Controllers\pages\content;
use App\Http\Controllers\pages\reimbursment\RembursPage;
use App\Http\Controllers\pages\performance\MyPerformance;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
  Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
  Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
  Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
  Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
  ->middleware('auth')
  ->name('logout');

// Protected Routes - All authenticated users
Route::middleware('auth')->group(function () {
  // Dashboard route - redirect ke dashboard sesuai role
  Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
      'admin' => redirect()->route('admin.dashboard'),
      'supervisor' => redirect()->route('supervisor.dashboard'),
      'manager' => redirect()->route('manager.dashboard'),
      default => redirect()->route('pages-home'),
    };
  })->name('dashboard');

  // Admin Dashboard - ADMIN ONLY
  Route::get('/admin-dashboard', [
    \App\Http\Controllers\Dashboard\DashboardController::class,
    'adminDashboard',
  ])
    ->middleware('role:admin')
    ->name('admin.dashboard');

  // Supervisor Dashboard - SUPERVISOR ONLY (admin can't access)
  Route::get('/supervisor-dashboard', [
    \App\Http\Controllers\Dashboard\DashboardController::class,
    'supervisorDashboard',
  ])
    ->middleware('role:supervisor')
    ->name('supervisor.dashboard');

  // Manager Dashboard - MANAGER ONLY
  Route::get('/manager-dashboard', [
    \App\Http\Controllers\Dashboard\DashboardController::class,
    'managerDashboard',
  ])
    ->middleware('role:manager')
    ->name('manager.dashboard');

  // Main Page Route
  Route::get('/', [HomePage::class, 'index'])->name('pages-home');
  Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');
  Route::get('/pages-tasks', [App\Http\Controllers\pages\tasks\TaskPage::class, 'index'])->name('pages-tasks');
  Route::get('/pages-content', [App\Http\Controllers\pages\content\PageContent::class, 'index'])->name('pages-content');
  Route::get('/pages-content-detail/{id}', [App\Http\Controllers\pages\content\PageContent::class, 'show'])->name(
    'pages-content.show'
  );
  Route::get('/pages-content-create', [App\Http\Controllers\pages\content\PageContent::class, 'create'])->name(
    'pages-content.create'
  );
  Route::post('/pages-content', [App\Http\Controllers\pages\content\PageContent::class, 'store'])->name(
    'pages-content.store'
  );
  Route::post('/pages-content/comment', [App\Http\Controllers\pages\content\PageContent::class, 'addComment'])->name(
    'pages-content.comment'
  );
  Route::get('/pages-reimbursment', [App\Http\Controllers\pages\reimbursment\RembursPage::class, 'index'])->name(
    'pages-reimburs'
  );
  Route::get('/pages-reimburs-create', [App\Http\Controllers\pages\reimbursment\RembursPage::class, 'create'])->name(
    'reimburs.create'
  );
  Route::post('/pages-reimburs', [App\Http\Controllers\pages\reimbursment\RembursPage::class, 'store'])->name(
    'reimburs.store'
  );
  Route::get('/pages-reimburs-detail/{id}', [
    App\Http\Controllers\pages\reimbursment\RembursPage::class,
    'detail',
  ])->name('reimburs.detail');
  Route::post('/pages-reimburs/{id}/approve', [
    App\Http\Controllers\pages\reimbursment\RembursPage::class,
    'approve',
  ])->name('reimburs.approve');
  Route::post('/pages-reimburs/{id}/reject', [
    App\Http\Controllers\pages\reimbursment\RembursPage::class,
    'reject',
  ])->name('reimburs.reject');
  Route::post('/pages-reimburs/{id}/payment-proof', [
    App\Http\Controllers\pages\reimbursment\RembursPage::class,
    'uploadPaymentProof',
  ])->name('reimburs.payment-proof');
  Route::get('/pages-performance', [App\Http\Controllers\pages\performance\MyPerformance::class, 'index'])->name(
    'pages-performance'
  );
  Route::get('/pages-performance/{id}', [App\Http\Controllers\pages\performance\MyPerformance::class, 'show'])->name(
    'pages-performance.show'
  );

  // User Management Routes (Admin only)
  Route::middleware('role:admin,superadmin')
    ->prefix('users')
    ->name('users.')
    ->group(function () {
      Route::get('/', [App\Http\Controllers\pages\UserController::class, 'index'])->name('index');
      Route::get('/create', [App\Http\Controllers\pages\UserController::class, 'create'])->name('create');
      Route::post('/', [App\Http\Controllers\pages\UserController::class, 'store'])->name('store');
      Route::get('/{user}/edit', [App\Http\Controllers\pages\UserController::class, 'edit'])->name('edit');
      Route::put('/{user}', [App\Http\Controllers\pages\UserController::class, 'update'])->name('update');
      Route::delete('/{user}', [App\Http\Controllers\pages\UserController::class, 'destroy'])->name('destroy');
      Route::post('/{user}/toggle-status', [App\Http\Controllers\pages\UserController::class, 'toggleStatus'])->name(
        'toggle-status'
      );
  });

  // Master Data Routes (Admin/Superadmin only)
  Route::middleware('role:admin,superadmin')
    ->group(function () {
      // Brands
      Route::resource('brands', App\Http\Controllers\BrandController::class)->except(['create', 'show', 'edit']);
      
      // Content Types
      Route::resource('content_types', App\Http\Controllers\ContentTypeController::class)->except(['create', 'show', 'edit']);
  });

  // Task Management Routes
  Route::prefix('tasks')
    ->name('tasks.')
    ->group(function () {
      Route::get('/', [App\Http\Controllers\TaskController::class, 'index'])->name('index');
      Route::get('/create', [App\Http\Controllers\TaskController::class, 'create'])->name('create');
      Route::post('/', [App\Http\Controllers\TaskController::class, 'store'])->name('store');
      Route::get('/{task}', [App\Http\Controllers\TaskController::class, 'show'])->name('show');
      Route::get('/{task}/edit', [App\Http\Controllers\TaskController::class, 'edit'])->name('edit');
      Route::put('/{task}', [App\Http\Controllers\TaskController::class, 'update'])->name('update');
      Route::post('/{task}/check-in', [App\Http\Controllers\TaskController::class, 'checkIn'])->name('check-in');
      Route::post('/{task}/check-out', [App\Http\Controllers\TaskController::class, 'checkOut'])->name('check-out');
      Route::post('/{task}/submit-review', [App\Http\Controllers\TaskController::class, 'submitForReview'])->name(
        'submit-review'
      );
      Route::post('/{task}/approve', [App\Http\Controllers\TaskController::class, 'approve'])->name('approve');
      Route::post('/{task}/reject', [App\Http\Controllers\TaskController::class, 'reject'])->name('reject');
      Route::post('/{task}/comment', [App\Http\Controllers\TaskController::class, 'addComment'])->name('comment');
      Route::post('/{task}/mark-completed', [App\Http\Controllers\TaskController::class, 'markCompleted'])->name(
        'mark-completed'
      );
    });

  // locale
  Route::get('lang/{locale}', [LanguageController::class, 'swap']);
});

// pages
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
