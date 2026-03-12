<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('headline');
            $table->text('subheadline')->nullable();
            $table->string('cta_primary_text', 100)->nullable();
            $table->string('cta_primary_url')->nullable();
            $table->string('cta_secondary_text', 100)->nullable();
            $table->string('cta_secondary_url')->nullable();
            $table->string('background_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
