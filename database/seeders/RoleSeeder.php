<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Superadmin', 'slug' => 'superadmin', 'description' => 'System Superadmin'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'System Administrator'],
            ['name' => 'Finance', 'slug' => 'finance', 'description' => 'Finance Department'],
            ['name' => 'Creative Director', 'slug' => 'creative_director', 'description' => 'Creative Director'],
            ['name' => 'Tim Internal', 'slug' => 'tim_internal', 'description' => 'Tim Internal Employee'],
            ['name' => 'Sosmed Spesialis', 'slug' => 'sosmed_spesialis', 'description' => 'Social Media Specialist'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
