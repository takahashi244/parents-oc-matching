<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('schools', function (Blueprint $table) {
      $table->string('school_id', 16)->primary();
      $table->string('school_name');
      $table->string('school_type', 32);
      $table->string('prefecture', 32);
    });
  }
  public function down(): void { Schema::dropIfExists('schools'); }
};
