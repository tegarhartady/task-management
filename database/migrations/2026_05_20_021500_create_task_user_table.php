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
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing assigned_to data
        $tasks = \Illuminate\Support\Facades\DB::table('tasks')->whereNotNull('assigned_to')->get();
        foreach ($tasks as $task) {
            \Illuminate\Support\Facades\DB::table('task_user')->insert([
                'task_id' => $task->id,
                'user_id' => $task->assigned_to,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop assigned_to column from tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users');
        });

        // Migrate data back
        $taskUsers = \Illuminate\Support\Facades\DB::table('task_user')->get();
        foreach ($taskUsers as $taskUser) {
            \Illuminate\Support\Facades\DB::table('tasks')
                ->where('id', $taskUser->task_id)
                ->update(['assigned_to' => $taskUser->user_id]);
        }

        Schema::dropIfExists('task_user');
    }
};
