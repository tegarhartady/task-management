<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

// Restore assigned_to column
if (!Schema::hasColumn('tasks', 'assigned_to')) {
    Schema::table('tasks', function (Blueprint $table) {
        $table->foreignId('assigned_to')->nullable()->constrained('users');
    });

    // Migrate data back
    if (Schema::hasTable('task_user')) {
        $taskUsers = DB::table('task_user')->get();
        foreach ($taskUsers as $taskUser) {
            DB::table('tasks')
                ->where('id', $taskUser->task_id)
                ->update(['assigned_to' => $taskUser->user_id]);
        }
    }
}

// Drop task_user table
Schema::dropIfExists('task_user');

// Delete migration record so it doesn't show up in artisan migrate:status
DB::table('migrations')->where('migration', 'like', '%create_task_user_table%')->delete();

echo "Database successfully restored to single assigned_to.\n";
