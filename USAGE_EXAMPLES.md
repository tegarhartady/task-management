# 📚 Contoh Penggunaan - Authentication & Role System

## Usage Examples - Views (Blade)

### 1. Cek Role di View

```blade
<!-- Check if admin -->
@if(auth()->user()->isAdmin())
    <p>Ini hanya untuk admin</p>
@endif

<!-- Check if supervisor -->
@if(auth()->user()->isSupervisor())
    <p>Supervisor area</p>
@endif

<!-- Check multiple roles -->
@if(auth()->user()->hasAnyRole(['admin', 'manager']))
    <p>Admin atau Manager saja</p>
@endif

<!-- Menggunakan helper (jika sudah didaftarkan) -->
@if(isAdmin())
    <p>Hanya admin yang melihat ini</p>
@endif
```

### 2. Display User Info

```blade
<!-- Current user name -->
{{ auth()->user()->name }}

<!-- Current user email -->
{{ auth()->user()->email }}

<!-- Current user role -->
{{ auth()->user()->role }}

<!-- User role dalam format label -->
{{ ucfirst(auth()->user()->role) }}
```

### 3. Role Badge di Tabel

```blade
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @if($user->isAdmin())
                        <span class="badge bg-danger">Admin</span>
                    @elseif($user->isSupervisor())
                        <span class="badge bg-warning">Supervisor</span>
                    @elseif($user->isManager())
                        <span class="badge bg-primary">Manager</span>
                    @else
                        <span class="badge bg-info">Karyawan</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### 4. Admin Only Links

```blade
<!-- Navbar Admin Links -->
<ul class="navbar-nav ms-auto">
    @if(auth()->check())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('pages-tasks') }}">
                Tasks
            </a>
        </li>

        <!-- Admin only -->
        @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    Admin Panel
                </a>
            </li>
        @endif

        <!-- Supervisor & Admin -->
        @if(auth()->user()->hasAnyRole(['supervisor', 'admin']))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('supervisor.dashboard') }}">
                    Supervision
                </a>
            </li>
        @endif
    @else
        <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">
                Login
            </a>
        </li>
    @endif
</ul>
```

---

## Usage Examples - Controllers

### 1. Get Current User Info

```php
<?php

namespace App\Http\Controllers;

class MyController extends Controller
{
  public function index()
  {
    $user = auth()->user();

    // User info
    $name = $user->name;
    $email = $user->email;
    $role = $user->role;

    // Check role
    if ($user->isAdmin()) {
      // Admin logic
    }

    return view('my-page', compact('user'));
  }
}
```

### 2. Role-Based Logic

```php
public function performAction()
{
    $user = auth()->user();

    if ($user->isAdmin()) {
        // Admin dapat melakukan apapun
        return $this->adminAction();
    } elseif ($user->isSupervisor()) {
        // Supervisor bisa supervise
        return $this->supervisorAction();
    } elseif ($user->isManager()) {
        // Manager manage team
        return $this->managerAction();
    } else {
        // Karyawan akses terbatas
        return $this->karyawanAction();
    }
}
```

### 3. Query Users by Role

```php
use App\Models\User;

public function getAdmins()
{
    // Get all admin users
    $admins = User::where('role', 'admin')->get();

    return view('admins.list', ['users' => $admins]);
}

public function getSupervisors()
{
    // Get active supervisors
    $supervisors = User::where('role', 'supervisor')
                        ->where('is_active', true)
                        ->get();

    return view('supervisors.list', ['users' => $supervisors]);
}

public function getTeam()
{
    // Get all karyawan under supervisor
    $team = User::where('role', 'karyawan')
                 ->where('is_active', true)
                 ->get();

    return view('team.list', ['users' => $team]);
}
```

### 4. Authorization Check

```php
public function update(User $user)
{
    // Check if admin
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized');
    }

    // Update logic here
    $user->update(request()->all());

    return redirect()->back()->with('success', 'Updated');
}
```

---

## Usage Examples - Middleware

### 1. Protect Admin Routes

```php
// routes/web.php

// Admin only
Route::group(['middleware' => 'auth', 'role:admin'], function () {
  Route::get('/admin/users', [AdminController::class, 'users']);
  Route::get('/admin/settings', [AdminController::class, 'settings']);
  Route::post('/admin/users', [AdminController::class, 'storeUser']);
});
```

### 2. Protect Multiple Roles

```php
// Accessible by admin or manager
Route::group(['middleware' => 'auth', 'role:admin,manager'], function () {
  Route::get('/management/team', [ManagementController::class, 'team']);
  Route::get('/management/reports', [ManagementController::class, 'reports']);
});
```

### 3. Protect Dashboard

```php
// Single dashboard route dengan role protection
Route::middleware('auth')->group(function () {
  Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->middleware('role:admin')
    ->name('admin.dashboard');

  Route::get('/manager/dashboard', [DashboardController::class, 'manager'])
    ->middleware('role:manager,admin')
    ->name('manager.dashboard');

  Route::get('/supervisor/dashboard', [DashboardController::class, 'supervisor'])
    ->middleware('role:supervisor,admin')
    ->name('supervisor.dashboard');
});
```

---

## Usage Examples - Models

### 1. User Model Methods

```php
// app/Models/User.php

<?php class User extends Authenticatable
{
  // ...existing code...

  public function isAdmin()
  {
    return $this->role === 'admin';
  }

  public function isSupervisor()
  {
    return $this->role === 'supervisor';
  }

  public function isManager()
  {
    return $this->role === 'manager';
  }

  public function isKaryawan()
  {
    return $this->role === 'karyawan';
  }

  public function hasRole($role)
  {
    return $this->role === $role;
  }

  public function hasAnyRole($roles)
  {
    return in_array($this->role, (array) $roles);
  }

  // Scopes
  public function scopeAdmin($query)
  {
    return $query->where('role', 'admin');
  }

  public function scopeSupervisor($query)
  {
    return $query->where('role', 'supervisor');
  }

  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }
}
```

### 2. Using Scopes

```php
use App\Models\User;

// Get all active admins
$activeAdmins = User::admin()
  ->active()
  ->get();

// Count supervisors
$supervisorCount = User::supervisor()->count();

// Find active user by email
$user = User::where('email', 'admin@example.com')
  ->active()
  ->first();
```

---

## Usage Examples - Helpers (Jika Menggunakan Helper Functions)

### 1. Check Role

```php
// app/Helpers/AuthHelpers.php

// Global function
if (isAdmin()) {
  echo 'User is admin';
}

// Get user role
$role = userRole(); // returns 'admin', 'supervisor', 'manager', 'karyawan'

// Check role
if (hasRole(['admin', 'manager'])) {
  // Admin or Manager only
}
```

### 2. Get Role Info

```php
// Get role color
$color = getRoleColor('admin'); // 'danger'

// Get role label
$label = getRoleLabel('supervisor'); // 'Supervisor'

// Get role icon
$icon = getRoleIcon('manager'); // 'ti-briefcase'

// Get dashboard route
$route = getRoleDashboardRoute('admin'); // '/admin-dashboard'
```

---

## Usage Examples - Artisan Commands

### 1. Create Admin User via Tinker

```bash
php artisan tinker
```

```php
// Di dalam tinker
$user = App\Models\User::create([
  'name' => 'John Admin',
  'email' => 'john.admin@example.com',
  'password' => Hash::make('secretpassword'),
  'role' => 'admin',
  'is_active' => true,
]);

// Verify
$user->isAdmin(); // true
```

### 2. Update User Role

```bash
php artisan tinker
```

```php
// Find user
$user = User::find(1);

// Change role
$user->update(['role' => 'manager']);

// Verify
$user->isManager(); // true
```

### 3. Deactivate User

```php
// Deactivate user
$user->update(['is_active' => false]);

// Reactivate user
$user->update(['is_active' => true]);
```

---

## Usage Examples - API (JSON Response)

```php
// Return user with role
public function profile()
{
    $user = auth()->user();

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'is_active' => $user->is_active,
        'is_admin' => $user->isAdmin(),
        'is_supervisor' => $user->isSupervisor(),
    ]);
}
```

---

## Common Patterns

### Pattern 1: Redirect Based on Role

```php
// routes/web.php
Route::middleware('auth')
  ->get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
      'admin' => redirect()->route('admin.dashboard'),
      'supervisor' => redirect()->route('supervisor.dashboard'),
      'manager' => redirect()->route('manager.dashboard'),
      default => redirect()->route('pages-home'),
    };
  })
  ->name('dashboard');
```

### Pattern 2: Role-Based View Selection

```php
// Controller
public function show($id)
{
    $data = SomeModel::find($id);

    $view = match (auth()->user()->role) {
        'admin' => 'admin.details',
        'manager' => 'manager.details',
        default => 'user.details',
    };

    return view($view, ['data' => $data]);
}
```

### Pattern 3: Permission Gate (Optional)

```php
// app/Providers/AuthServiceProvider.php
public function boot()
{
    Gate::define('admin-access', function (User $user) {
        return $user->isAdmin();
    });
}

// Usage in route
Route::middleware('can:admin-access')->get('/admin', ...);

// Usage in view
@can('admin-access')
    <p>Admin only</p>
@endcan
```

---

**Tips & Best Practices:**

- ✅ Always check `auth()->check()` sebelum akses `auth()->user()`
- ✅ Gunakan middleware untuk route protection, bukan logic di controller
- ✅ Implement scopes untuk query yang diulang
- ✅ Keep role strings consistent (use constants jika perlu)
- ✅ Test semua role scenarios sebelum production
