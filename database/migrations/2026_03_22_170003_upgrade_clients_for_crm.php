<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete()->after('id');
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete()->after('lead_id');
            $table->string('billing_type')->default('prepaid')->after('contract_end');
            $table->string('payment_frequency')->default('monthly')->after('billing_type');
            $table->decimal('package_price', 12, 2)->nullable()->after('payment_frequency');
            $table->string('billing_status')->default('active')->after('package_price');
            $table->string('activation_status')->default('pending')->after('billing_status');
            $table->date('activation_date')->nullable()->after('activation_status');
            $table->foreignId('lead_support_person')->nullable()->constrained('users')->nullOnDelete()->after('activation_date');
            $table->foreignId('secondary_support')->nullable()->constrained('users')->nullOnDelete()->after('lead_support_person');
            $table->string('support_level')->default('standard')->after('secondary_support');
            $table->integer('sla_response_hours')->default(24)->after('support_level');
            $table->string('implementation_status')->default('not_started')->after('sla_response_hours');
            $table->integer('implementation_progress')->default(0)->after('implementation_status');
            $table->string('client_priority')->default('standard')->after('implementation_progress');
            $table->string('pipeline_stage')->default('active')->after('client_priority');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['deal_id']);
            $table->dropForeign(['lead_support_person']);
            $table->dropForeign(['secondary_support']);
            $table->dropColumn([
                'lead_id', 'deal_id', 'billing_type', 'payment_frequency',
                'package_price', 'billing_status', 'activation_status',
                'activation_date', 'lead_support_person', 'secondary_support',
                'support_level', 'sla_response_hours', 'implementation_status',
                'implementation_progress', 'client_priority', 'pipeline_stage',
            ]);
        });
    }
};
