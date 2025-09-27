<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('oc_events', function (Blueprint $table) {
      $table->string('ocev_id', 16)->primary();
      $table->string('dept_id', 16);
      $table->date('date');
      $table->time('start_time')->nullable();
      $table->time('end_time')->nullable();
      $table->string('place')->nullable();
      $table->string('reservation_url')->nullable();
      $table->foreign('dept_id')->references('dept_id')->on('departments')->cascadeOnDelete();
      $table->index(['dept_id','date']);
    });
  }
  public function down(): void { Schema::dropIfExists('oc_events'); }
};
