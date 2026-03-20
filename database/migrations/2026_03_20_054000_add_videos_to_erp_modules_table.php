<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_modules', function (Blueprint $table) {
            $table->longText('long_description')->nullable()->after('description');
            $table->json('youtube_videos')->nullable()->after('features');
        });
    }

    public function down(): void
    {
        Schema::table('erp_modules', function (Blueprint $table) {
            $table->dropColumn(['long_description', 'youtube_videos']);
        });
    }
};
