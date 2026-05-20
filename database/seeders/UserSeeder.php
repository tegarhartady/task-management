<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Admin User
    User::firstOrCreate(
      ['email' => 'admin@example.com'],
      [
        'name' => 'Tegar Hartady',
        'password' => Hash::make('password123'),
        'role' => 'admin',
        'is_active' => true,
      ]
    );

    // Supervisor User
    User::firstOrCreate(
      ['email' => 'supervisor@example.com'],
      [
        'name' => 'Rangga Supervisor',
        'password' => Hash::make('password123'),
        'role' => 'supervisor',
        'is_active' => true,
      ]
    );

    // Manager User
    User::firstOrCreate(
      ['email' => 'manager@example.com'],
      [
        'name' => 'Fernen Manager',
        'password' => Hash::make('password123'),
        'role' => 'manager',
        'is_active' => true,
      ]
    );

    // Regular Karyawan Users
    $karyawanUsers = [
      ['name' => 'Dila Karyawan', 'email' => 'dila@example.com'],
      ['name' => 'Budi Karyawan', 'email' => 'budi@example.com'],
      ['name' => 'Siti Karyawan', 'email' => 'siti@example.com'],
      ['name' => 'Ahmad Karyawan', 'email' => 'ahmad@example.com'],
    ];

    foreach ($karyawanUsers as $user) {
      User::firstOrCreate(
        ['email' => $user['email']],
        [
          'name' => $user['name'],
          'password' => Hash::make('password123'),
          'role' => 'karyawan',
          'is_active' => true,
        ]
      );
    }
  }
}
