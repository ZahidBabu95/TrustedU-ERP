<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_invoices', function (Blueprint $table) {
            $table->string('company_name', 255)->nullable()->after('title');
            $table->string('company_address', 500)->nullable()->after('company_name');
            $table->string('company_phone', 50)->nullable()->after('company_address');
            $table->string('company_email', 255)->nullable()->after('company_phone');
            $table->string('client_name', 255)->nullable()->after('company_email');
            $table->string('client_address', 500)->nullable()->after('client_name');
            $table->string('client_phone', 50)->nullable()->after('client_address');
            $table->string('client_email', 255)->nullable()->after('client_phone');
            $table->string('payment_method', 50)->nullable()->after('paid_amount');
            $table->text('terms_conditions')->nullable()->after('notes');
            $table->foreignId('migration_id')->nullable()->after('billing_plan_id')->constrained('crm_migrations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'company_name', 'company_address', 'company_phone', 'company_email',
                'client_name', 'client_address', 'client_phone', 'client_email',
                'payment_method', 'terms_conditions', 'migration_id',
            ]);
        });
    }
};
