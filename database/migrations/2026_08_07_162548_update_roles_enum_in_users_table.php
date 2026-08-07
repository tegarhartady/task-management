<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            return;
        }
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'tim_internal'");
        
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'manager')->update(['role' => 'finance']);
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'supervisor')->update(['role' => 'creative_director']);
        \Illuminate\Support\Facades\DB::table('users')->where('role', 'karyawan')->update(['role' => 'tim_internal']);
        \Illuminate\Support\Facades\DB::table('users')->whereNotIn('role', ['superadmin', 'admin', 'finance', 'creative_director', 'tim_internal', 'sosmed_spesialis'])->update(['role' => 'tim_internal']);

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'finance', 'creative_director', 'tim_internal', 'sosmed_spesialis') NOT NULL DEFAULT 'tim_internal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            return;
        }

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'karyawan'");
        \Illuminate\Support\Facades\DB::table('users')->whereNotIn('role', ['superadmin', 'admin', 'supervisor', 'manager', 'karyawan'])->update(['role' => 'karyawan']);
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'supervisor', 'manager', 'karyawan') NOT NULL DEFAULT 'karyawan'");
    }
};
