<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100)->index(); // e.g. 'modules', 'pricing', 'support', 'general'
            $table->string('question', 500);           // The question or topic
            $table->text('answer');                     // The answer/response
            $table->json('keywords')->nullable();       // Keywords for matching ['ডেমো', 'demo', 'trial']
            $table->enum('language', ['bn', 'en', 'both'])->default('both');
            $table->integer('priority')->default(0);   // Higher = more relevant
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // Track how often this is used
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge_bases');
    }
};
