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
        Schema::table('erp_modules', function (Blueprint $table) {
            $table->json('dynamic_sections')->nullable()->after('youtube_videos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('erp_modules', function (Blueprint $table) {
            $table->dropColumn('dynamic_sections');
        });
    }
};
