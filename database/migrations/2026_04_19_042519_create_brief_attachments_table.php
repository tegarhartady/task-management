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
    Schema::create('brief_attachments', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('brief_id');
      $table->string('file_path');
      $table->string('original_name');
      $table->enum('file_type', ['image', 'pdf', 'document'])->default('image');
      $table->timestamps();

      $table
        ->foreign('brief_id')
        ->references('id')
        ->on('briefs')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('brief_attachments');
  }
};
