<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_migrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->string('previous_software_name');
            $table->string('previous_software_vendor')->nullable();
            $table->json('data_categories')->nullable();
            $table->string('data_collection_method', 50)->default('manual');
            $table->string('data_format', 100)->nullable();
            $table->string('data_volume_estimate')->nullable();
            $table->date('migration_start_date');
            $table->date('migration_end_date');
            $table->integer('buffer_days')->default(5);
            $table->date('actual_end_date')->nullable();
            $table->date('decommission_date')->nullable();
            $table->string('old_system_status', 30)->default('running');
            $table->string('status', 30)->default('not_started');
            $table->integer('progress_percent')->default(0);
            $table->string('verification_status', 30)->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('signoff_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('signoff_at')->nullable();
            $table->text('risk_notes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_migrations');
    }
};
