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
    Schema::create('reimbursements', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->text('description');
      $table->string('category');
      $table->decimal('amount', 15, 2);
      $table->date('date');
      $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
      $table
        ->foreignId('submitted_by')
        ->constrained('users')
        ->onDelete('cascade');
      $table
        ->foreignId('approved_by')
        ->nullable()
        ->constrained('users')
        ->onDelete('set null');
      $table->text('rejection_reason')->nullable();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reimbursements');
  }
};
