<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('oc_memos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ocev_id', 16);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('role', ['parent','student'])->default('parent');
            $table->json('ratings')->nullable();  // {access:3, explain:4, ...}
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['ocev_id', 'role']);
        });
    }
    public function down(): void { Schema::dropIfExists('oc_memos'); }
};
