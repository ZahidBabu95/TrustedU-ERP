<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot: which ERP modules a client uses
        Schema::create('client_erp_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('erp_module_id')->constrained()->cascadeOnDelete();
            $table->date('activated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['client_id', 'erp_module_id']);
        });

        // Monthly active student stats per client
        Schema::create('client_monthly_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('active_students')->default(0);
            $table->unsignedInteger('total_students')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['client_id', 'year', 'month']);
        });

        // Extra profile fields for clients
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->nullable()->after('website');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('principal_name')->nullable()->after('address');
            $table->string('principal_phone')->nullable()->after('principal_name');
            $table->date('contract_start')->nullable()->after('principal_phone');
            $table->date('contract_end')->nullable()->after('contract_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_erp_module');
        Schema::dropIfExists('client_monthly_stats');

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'address',
                'principal_name', 'principal_phone',
                'contract_start', 'contract_end',
            ]);
        });
    }
};
