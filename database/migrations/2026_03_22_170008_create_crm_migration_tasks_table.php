<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_migration_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('migration_id')->constrained('crm_migrations')->cascadeOnDelete();
            $table->string('task_category', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('priority', 20)->default('medium');
            $table->string('status', 30)->default('pending');
            $table->date('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_path', 500)->nullable();
            $table->string('file_disk', 50)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['migration_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_migration_tasks');
    }
};
