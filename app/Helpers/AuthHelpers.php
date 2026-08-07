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

if (!function_exists('isFinance')) {
  /**
   * Check if current user is finance
   *
   * @return bool
   */
  function isFinance()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isFinance();
  }
}

if (!function_exists('isCreativeDirector')) {
  /**
   * Check if current user is creative director
   *
   * @return bool
   */
  function isCreativeDirector()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isCreativeDirector();
  }
}

if (!function_exists('isTimInternal')) {
  /**
   * Check if current user is tim internal
   *
   * @return bool
   */
  function isTimInternal()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isTimInternal();
  }
}

if (!function_exists('isSosmedSpesialis')) {
  /**
   * Check if current user is sosmed spesialis
   *
   * @return bool
   */
  function isSosmedSpesialis()
  {
    return auth()->check() &&
      auth()
        ->user()
        ->isSosmedSpesialis();
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
      'superadmin' => 'dark',
      'admin' => 'danger',
      'finance' => 'success',
      'creative_director' => 'primary',
      'tim_internal' => 'info',
      'sosmed_spesialis' => 'warning',
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
      'superadmin' => 'Superadmin',
      'admin' => 'Admin',
      'finance' => 'Finance',
      'creative_director' => 'Creative Director',
      'tim_internal' => 'Tim Internal',
      'sosmed_spesialis' => 'Sosmed Spesialis',
      default => ucfirst(str_replace('_', ' ', $role)),
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
      'superadmin' => 'ti-shield',
      'admin' => 'ti-crown',
      'finance' => 'ti-wallet',
      'creative_director' => 'ti-palette',
      'tim_internal' => 'ti-users',
      'sosmed_spesialis' => 'ti-brand-instagram',
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
      'superadmin' => route('admin.dashboard'),
      'admin' => route('admin.dashboard'),
      'creative_director' => route('admin.dashboard'),
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
      'finance' => 'Finance',
      'creative_director' => 'Creative Director',
      'tim_internal' => 'Tim Internal',
      'sosmed_spesialis' => 'Sosmed Spesialis',
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
