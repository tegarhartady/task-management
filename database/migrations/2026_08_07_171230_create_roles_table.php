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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Hapus ENUM pada users.role dan ubah menjadi VARCHAR biasa
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) NOT NULL DEFAULT 'tim_internal'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke ENUM jika rollback
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'tim_internal'");
        \Illuminate\Support\Facades\DB::table('users')->whereNotIn('role', ['superadmin', 'admin', 'finance', 'creative_director', 'tim_internal', 'sosmed_spesialis'])->update(['role' => 'tim_internal']);
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'finance', 'creative_director', 'tim_internal', 'sosmed_spesialis') NOT NULL DEFAULT 'tim_internal'");

        Schema::dropIfExists('roles');
    }
};
