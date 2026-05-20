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
    Schema::create('reimbursement_attachments', function (Blueprint $table) {
      $table->id();
      $table
        ->foreignId('reimbursement_id')
        ->constrained('reimbursements')
        ->onDelete('cascade');
      $table->string('file_path');
      $table->string('original_name');
      $table->string('file_type'); // 'image' or 'pdf' or 'document'
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reimbursement_attachments');
  }
};
