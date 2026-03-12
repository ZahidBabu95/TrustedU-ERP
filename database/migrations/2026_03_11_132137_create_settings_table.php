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
        Schema::create('settings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('key')->unique();
            $blueprint->string('label')->nullable();
            $blueprint->text('value')->nullable();
            $blueprint->string('type')->default('text'); // text, textarea, file
            $blueprint->string('group')->default('general'); // general, contact, social
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
