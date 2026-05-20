<?php

/**
 * Helper functions untuk authentication & role management
 *
 * Tambahkan ke app/Helpers/Helpers.php atau buat file terpisah
 * dan include di config/app.php providers
 */

if (!function_exists('userRole')) {
  /**
   * Get current user role
   *
   * @return string|null
   */
  function userRole()
  {
    return auth()->check() ? auth()->user()->role : null;
  }
}

if (!function_exists('isAdmin')) {
  /**
   * Check if current user is admin
   *
   * @return bool
   */
  function isAdmin()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isAdmin();
  }
}

if (!function_exists('isSupervisor')) {
  /**
   * Check if current user is supervisor
   *
   * @return bool
   */
  function isSupervisor()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isSupervisor();
  }
}

if (!function_exists('isManager')) {
  /**
   * Check if current user is manager
   *
   * @return bool
   */
  function isManager()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isManager();
  }
}

if (!function_exists('isKaryawan')) {
  /**
   * Check if current user is karyawan
   *
   * @return bool
   */
  function isKaryawan()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isKaryawan();
  }
}

if (!function_exists('hasRole')) {
  /**
   * Check if current user has specific role
   *
   * @param string|array $role
   * @return bool
   */
  function hasRole($role)
  {
    if (!auth()->check()) {
      return false;
    }

    return auth()
      ->user()
      ->hasAnyRole($role);
  }
}

if (!function_exists('getRoleColor')) {
  /**
   * Get color for role badge
   *
   * @param string $role
   * @return string
   */
  function getRoleColor($role)
  {
    return match ($role) {
      'admin' => 'danger',
      'supervisor' => 'warning',
      'manager' => 'primary',
      'karyawan' => 'info',
      default => 'secondary',
    };
  }
}

if (!function_exists('getRoleLabel')) {
  /**
   * Get label for role (with capitalization)
   *
   * @param string $role
   * @return string
   */
  function getRoleLabel($role)
  {
    return match ($role) {
      'admin' => 'Admin',
      'supervisor' => 'Supervisor',
      'manager' => 'Manager',
      'karyawan' => 'Karyawan',
      default => ucfirst($role),
    };
  }
}

if (!function_exists('getRoleIcon')) {
  /**
   * Get icon for role
   *
   * @param string $role
   * @return string
   */
  function getRoleIcon($role)
  {
    return match ($role) {
      'admin' => 'ti-crown',
      'supervisor' => 'ti-radar',
      'manager' => 'ti-briefcase',
      'karyawan' => 'ti-user',
      default => 'ti-user-circle',
    };
  }
}

if (!function_exists('getRoleDashboardRoute')) {
  /**
   * Get dashboard route for specific role
   *
   * @param string $role
   * @return string
   */
  function getRoleDashboardRoute($role)
  {
    return match ($role) {
      'admin' => route('admin.dashboard'),
      'supervisor' => route('supervisor.dashboard'),
      'manager' => route('manager.dashboard'),
      default => route('pages-home'),
    };
  }
}

if (!function_exists('getAvailableRoles')) {
  /**
   * Get all available roles
   *
   * @return array
   */
  function getAvailableRoles()
  {
    return [
      'admin' => 'Admin',
      'supervisor' => 'Supervisor',
      'manager' => 'Manager',
      'karyawan' => 'Karyawan',
    ];
  }
}

if (!function_exists('checkUserActive')) {
  /**
   * Check if user is active
   *
   * @param \App\Models\User $user
   * @return bool
   */
  function checkUserActive($user)
  {
    return $user->is_active === true;
  }
}
