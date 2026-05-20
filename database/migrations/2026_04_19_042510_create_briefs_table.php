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
    Schema::create('briefs', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('brand'); // SUPERNATA, DEKORNATA, CRAFTNATA
      $table->string('type'); // REEL, IGS, POST, CAROUSEL
      $table->enum('status', ['DRAFT', 'TAKEN', 'SUBMITTED', 'REVISION', 'APPROVED'])->default('DRAFT');
      $table->text('hook')->nullable();
      $table->longText('concept')->nullable();
      $table->longText('visual_direction')->nullable();
      $table->longText('voiceover')->nullable();
      $table->unsignedBigInteger('created_by');
      $table->unsignedBigInteger('assigned_to')->nullable();
      $table->integer('comments')->default(0);
      $table->boolean('is_ai')->default(false);
      $table->timestamps();

      $table
        ->foreign('created_by')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
      $table
        ->foreign('assigned_to')
        ->references('id')
        ->on('users')
        ->onDelete('set null');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('briefs');
  }
};
