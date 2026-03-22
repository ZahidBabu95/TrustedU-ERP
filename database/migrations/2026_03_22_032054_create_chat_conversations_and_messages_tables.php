<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->enum('status', ['active', 'closed', 'escalated'])->default('active');
            $table->string('subject')->nullable();
            $table->text('summary')->nullable();
            $table->integer('message_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->enum('role', ['user', 'bot', 'agent'])->default('user');
            $table->text('message');
            $table->json('metadata')->nullable(); // AI model info, confidence, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};
