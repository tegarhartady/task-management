<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    // Tasks Table
    Schema::create('tasks', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->text('description')->nullable();
      $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
      $table
        ->enum('status', ['Not Started', 'In Progress', 'Pending Review', 'Approved', 'Completed', 'Rejected'])
        ->default('Not Started');
      $table->foreignId('created_by')->constrained('users');
      $table
        ->foreignId('assigned_to')
        ->nullable()
        ->constrained('users');
      $table
        ->foreignId('reviewed_by')
        ->nullable()
        ->constrained('users');
      $table->date('due_date')->nullable();
      $table->dateTime('checked_in_at')->nullable();
      $table->dateTime('checked_out_at')->nullable();
      $table
        ->integer('progress')
        ->default(0)
        ->comment('0-100');
      $table->timestamps();
    });

    // Task Attachments Table
    Schema::create('task_attachments', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('task_id')
        ->constrained('tasks')
        ->onDelete('cascade');
      $table->enum('type', ['image', 'document', 'link'])->default('document');
      $table->string('file_path')->nullable();
      $table->string('link')->nullable();
      $table->string('original_name')->nullable();
      $table->foreignId('uploaded_by')->constrained('users');
      $table->timestamps();
    });

    // Task Comments Table
    Schema::create('task_comments', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('task_id')
        ->constrained('tasks')
        ->onDelete('cascade');
      $table->text('comment');
      $table->enum('type', ['comment', 'status_change', 'approval', 'rejection'])->default('comment');
      $table->foreignId('user_id')->constrained('users');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('task_comments');
    Schema::dropIfExists('task_attachments');
    Schema::dropIfExists('tasks');
  }
};
