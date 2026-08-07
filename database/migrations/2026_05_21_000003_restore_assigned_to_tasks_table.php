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
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        $tasks = DB::table('tasks')->whereNotNull('assigned_to')->get();
        foreach ($tasks as $task) {
            DB::table('task_user')->insert([
                'task_id' => $task->id,
                'user_id' => $task->assigned_to,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('tasks', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['assigned_to']);
            }
            $table->dropColumn('assigned_to');
        });
    }
};
