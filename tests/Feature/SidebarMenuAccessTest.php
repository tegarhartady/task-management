<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class SidebarMenuAccessTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole($role)
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_allowed_menus()
    {
        $admin = $this->createUserWithRole('admin');

        $this->actingAs($admin)->get('/admin-dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/tasks')->assertStatus(200);
        $this->actingAs($admin)->get('/pages-content')->assertStatus(200);
        $this->actingAs($admin)->get('/pages-performance')->assertStatus(200);
        $this->actingAs($admin)->get('/pages-reimbursment')->assertStatus(200);
        $this->actingAs($admin)->get('/brands')->assertStatus(200);
        $this->actingAs($admin)->get('/content_types')->assertStatus(200);
        $this->actingAs($admin)->get('/users')->assertStatus(200);

        // Admins should not access supervisor and manager dashboard
        $this->actingAs($admin)->get('/supervisor-dashboard')->assertStatus(403);
        $this->actingAs($admin)->get('/manager-dashboard')->assertStatus(403);
    }

    public function test_supervisor_can_access_allowed_menus()
    {
        $supervisor = $this->createUserWithRole('supervisor');

        $this->actingAs($supervisor)->get('/supervisor-dashboard')->assertStatus(200);
        $this->actingAs($supervisor)->get('/tasks')->assertStatus(200);
        $this->actingAs($supervisor)->get('/pages-content')->assertStatus(200);
        $this->actingAs($supervisor)->get('/pages-performance')->assertStatus(200);
        $this->actingAs($supervisor)->get('/pages-reimbursment')->assertStatus(200);

        // Supervisors should not access admin menus
        $this->actingAs($supervisor)->get('/admin-dashboard')->assertStatus(403);
        $this->actingAs($supervisor)->get('/users')->assertStatus(403);
        $this->actingAs($supervisor)->get('/brands')->assertStatus(403);
    }

    public function test_manager_can_access_allowed_menus()
    {
        $manager = $this->createUserWithRole('manager');

        $this->actingAs($manager)->get('/manager-dashboard')->assertStatus(200);
        $this->actingAs($manager)->get('/tasks')->assertStatus(200);
        $this->actingAs($manager)->get('/pages-content')->assertStatus(200);
        $this->actingAs($manager)->get('/pages-performance')->assertStatus(200);
        $this->actingAs($manager)->get('/pages-reimbursment')->assertStatus(200);

        // Managers should not access admin/supervisor dashboard
        $this->actingAs($manager)->get('/admin-dashboard')->assertStatus(403);
        $this->actingAs($manager)->get('/supervisor-dashboard')->assertStatus(403);
        $this->actingAs($manager)->get('/users')->assertStatus(403);
    }

    public function test_karyawan_can_access_allowed_menus()
    {
        $karyawan = $this->createUserWithRole('karyawan');

        $this->actingAs($karyawan)->get('/')->assertStatus(200);
        $this->actingAs($karyawan)->get('/tasks')->assertStatus(200);
        $this->actingAs($karyawan)->get('/pages-content')->assertStatus(200);
        $this->actingAs($karyawan)->get('/pages-performance')->assertStatus(200);
        $this->actingAs($karyawan)->get('/pages-reimbursment')->assertStatus(200);

        // Karyawan should not access higher level dashboard and masters
        $this->actingAs($karyawan)->get('/admin-dashboard')->assertStatus(403);
        $this->actingAs($karyawan)->get('/supervisor-dashboard')->assertStatus(403);
        $this->actingAs($karyawan)->get('/brands')->assertStatus(403);
    }
}
