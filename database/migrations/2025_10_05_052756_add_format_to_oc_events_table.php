<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oc_events', function (Blueprint $table) {
            if (!Schema::hasColumn('oc_events', 'format')) {
                $table->string('format', 20)->nullable()->after('place');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oc_events', function (Blueprint $table) {
            if (Schema::hasColumn('oc_events', 'format')) {
                $table->dropColumn('format');
            }
        });
    }
};
