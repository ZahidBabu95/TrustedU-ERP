<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_migrations', function (Blueprint $table) {
            // Make deal_id nullable for lead-based migrations
            $table->foreignId('lead_id')->nullable()->after('client_id')->constrained('leads')->nullOnDelete();
            $table->string('current_step', 50)->default('onboarding_plan')->after('status');
            $table->json('onboarding_plan')->nullable()->after('notes');
            $table->json('checklist_items')->nullable()->after('onboarding_plan');
            $table->json('training_data')->nullable()->after('checklist_items');
            $table->json('handover_data')->nullable()->after('training_data');
            $table->json('invoice_data')->nullable()->after('handover_data');
            $table->string('previous_software_name')->nullable()->change();
            $table->date('migration_start_date')->nullable()->change();
            $table->date('migration_end_date')->nullable()->change();
        });

        // Make deal_id nullable
        Schema::table('crm_migrations', function (Blueprint $table) {
            $table->foreignId('deal_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('crm_migrations', function (Blueprint $table) {
            $table->dropColumn(['lead_id', 'current_step', 'onboarding_plan', 'checklist_items', 'training_data', 'handover_data', 'invoice_data']);
        });
    }
};
