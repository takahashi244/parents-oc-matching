<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share_links', function (Blueprint $table) {
            $table->string('token', 64)->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('payload');
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_links');
    }
};
