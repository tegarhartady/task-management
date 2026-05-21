<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'assigned_to')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreignId('assigned_to')->nullable()->constrained('users');
            });

            // Pindahkan kembali datanya jika tabel task_user masih ada
            if (Schema::hasTable('task_user')) {
                $taskUsers = DB::table('task_user')->get();
                foreach ($taskUsers as $taskUser) {
                    DB::table('tasks')
                        ->where('id', $taskUser->task_id)
                        ->update(['assigned_to' => $taskUser->user_id]);
                }
            }
        }

        // Hapus tabel eksperimen multi-assign
        Schema::dropIfExists('task_user');
    }

    public function down(): void
    {
        // No rollback needed for this fix
    }
};
