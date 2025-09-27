<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('rev_id', 16)->primary();
            $table->string('ocev_id', 16);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('author_role', ['parent','student'])->default('parent');
            $table->unsignedTinyInteger('rating')->nullable(); // 1..5
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['ocev_id', 'is_published']);
        });
    }
    public function down(): void { Schema::dropIfExists('reviews'); }
};
