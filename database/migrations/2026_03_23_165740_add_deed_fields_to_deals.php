<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('deed_number', 50)->nullable()->after('title');
            $table->string('deed_status', 30)->default('not_created')->after('deed_number');
            $table->date('deed_effective_date')->nullable()->after('deed_status');
            $table->date('deed_end_date')->nullable()->after('deed_effective_date');
            $table->string('deed_duration', 20)->nullable()->after('deed_end_date');
            $table->string('deed_plan_name', 100)->nullable()->after('deed_duration');
            $table->json('deed_plan_features')->nullable()->after('deed_plan_name');
            $table->decimal('deed_monthly_fee', 12, 2)->default(0)->after('deed_plan_features');
            $table->decimal('deed_per_user_rate', 10, 2)->default(15)->after('deed_monthly_fee');
            $table->decimal('deed_installation_cost', 10, 2)->default(4000)->after('deed_per_user_rate');
            $table->integer('deed_total_users')->nullable()->after('deed_installation_cost');
            $table->string('deed_client_representative', 255)->nullable()->after('deed_total_users');
            $table->string('deed_client_designation', 100)->nullable()->after('deed_client_representative');
            $table->text('deed_client_address')->nullable()->after('deed_client_designation');
            $table->json('deed_bank_accounts')->nullable()->after('deed_client_address');
            $table->json('deed_company_info')->nullable()->after('deed_bank_accounts');
            $table->text('deed_notes')->nullable()->after('deed_company_info');
            $table->dateTime('deed_generated_at')->nullable()->after('deed_notes');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn([
                'deed_number', 'deed_status', 'deed_effective_date', 'deed_end_date',
                'deed_duration', 'deed_plan_name', 'deed_plan_features', 'deed_monthly_fee',
                'deed_per_user_rate', 'deed_installation_cost', 'deed_total_users',
                'deed_client_representative', 'deed_client_designation', 'deed_client_address',
                'deed_bank_accounts', 'deed_company_info', 'deed_notes', 'deed_generated_at',
            ]);
        });
    }
};
