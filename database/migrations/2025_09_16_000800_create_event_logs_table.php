<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('event_logs', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('event', 64);
      $table->json('props')->nullable();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->string('role', 16)->nullable();
      $table->timestamp('created_at')->useCurrent();
      $table->index(['event','created_at']);
    });
  }
  public function down(): void { Schema::dropIfExists('event_logs'); }
};
