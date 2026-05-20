<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = ['password', 'remember_token'];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  /**
   * Check if user has a specific role
   */
  public function hasRole($role)
  {
    return $this->role === $role;
  }

  /**
   * Check if user has any of the given roles
   */
  public function hasAnyRole($roles)
  {
    return in_array($this->role, (array) $roles);
  }

  /**
   * Check if user is superadmin
   */
  public function isSuperadmin()
  {
    return $this->role === 'superadmin';
  }

  /**
   * Check if user is admin or superadmin
   */
  public function isAdmin()
  {
    return in_array($this->role, ['superadmin', 'admin']);
  }

  /**
   * Check if user is supervisor
   */
  public function isSupervisor()
  {
    return $this->role === 'supervisor';
  }

  /**
   * Check if user is manager
   */
  public function isManager()
  {
    return $this->role === 'manager';
  }

  /**
   * Check if user is karyawan
   */
  public function isKaryawan()
  {
    return $this->role === 'karyawan';
  }

  /**
   * Relationships
   */
  public function tasks()
  {
    return $this->hasMany(Task::class, 'assigned_to');
  }

  public function createdTasks()
  {
    return $this->hasMany(Task::class, 'created_by');
  }

  public function reviewedTasks()
  {
    return $this->hasMany(Task::class, 'reviewed_by');
  }

  public function briefs()
  {
    return $this->hasMany(Brief::class, 'created_by');
  }

  public function assignedBriefs()
  {
    return $this->hasMany(Brief::class, 'assigned_to');
  }
}
