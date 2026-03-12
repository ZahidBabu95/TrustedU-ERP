<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->timestamps();
        });

        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('contact_name', 150);
            $table->string('email', 150);
            $table->string('phone', 30);
            $table->string('institution_name', 200);
            $table->enum('institution_type', ['school', 'college', 'university', 'madrasha', 'other'])->nullable();
            $table->string('district', 100)->nullable();
            $table->string('student_count', 50)->nullable();
            $table->json('interested_modules')->nullable();
            $table->date('preferred_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'contacted', 'demo_done', 'converted', 'rejected'])->default('pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 100)->default('landing_page');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
        Schema::dropIfExists('contact_messages');
    }
};
