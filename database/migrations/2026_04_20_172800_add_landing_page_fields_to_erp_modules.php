<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_modules', function (Blueprint $table) {
            $table->string('hero_subtitle', 300)->nullable()->after('description');
            $table->string('hero_image')->nullable()->after('hero_subtitle');
            $table->string('download_url')->nullable()->after('youtube_videos');
            $table->string('download_label', 100)->nullable()->after('download_url');
        });
    }

    public function down(): void
    {
        Schema::table('erp_modules', function (Blueprint $table) {
            $table->dropColumn(['hero_subtitle', 'hero_image', 'download_url', 'download_label']);
        });
    }
};
