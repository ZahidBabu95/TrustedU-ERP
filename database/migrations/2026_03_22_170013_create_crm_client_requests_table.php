<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_client_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('request_number', 50)->unique();
            $table->string('request_type', 30)->default('feature');
            $table->string('title');
            $table->text('description');
            $table->boolean('is_paid')->default(false);
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->string('approval_status', 30)->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('implementation_status', 30)->default('not_started');
            $table->string('priority', 20)->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('approval_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_client_requests');
    }
};
