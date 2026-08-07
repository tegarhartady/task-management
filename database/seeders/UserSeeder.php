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

    // Finance User
    User::firstOrCreate(
      ['email' => 'finance@example.com'],
      [
        'name' => 'Fernen Finance',
        'password' => Hash::make('password123'),
        'role' => 'finance',
        'is_active' => true,
      ]
    );

    // Creative Director User
    User::firstOrCreate(
      ['email' => 'creative@example.com'],
      [
        'name' => 'Rangga Creative',
        'password' => Hash::make('password123'),
        'role' => 'creative_director',
        'is_active' => true,
      ]
    );
    
    // Sosmed Spesialis User
    User::firstOrCreate(
      ['email' => 'sosmed@example.com'],
      [
        'name' => 'Dila Sosmed',
        'password' => Hash::make('password123'),
        'role' => 'sosmed_spesialis',
        'is_active' => true,
      ]
    );

    // Tim Internal Users
    $timInternalUsers = [
      ['name' => 'Budi Internal', 'email' => 'budi@example.com'],
      ['name' => 'Siti Internal', 'email' => 'siti@example.com'],
      ['name' => 'Ahmad Internal', 'email' => 'ahmad@example.com'],
    ];

    foreach ($timInternalUsers as $user) {
      User::firstOrCreate(
        ['email' => $user['email']],
        [
          'name' => $user['name'],
          'password' => Hash::make('password123'),
          'role' => 'tim_internal',
          'is_active' => true,
        ]
      );
    }
  }
}
