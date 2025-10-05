<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('departments', function (Blueprint $table) {
      $table->string('dept_id', 16)->primary();
      $table->string('school_id', 16);
      $table->string('dept_name');
      $table->string('tags', 255)->nullable();
      $table->text('summary')->nullable();
      $table->foreign('school_id')->references('school_id')->on('schools')->cascadeOnDelete();
      $table->index('school_id');
    });
  }
  public function down(): void { Schema::dropIfExists('departments'); }
};
