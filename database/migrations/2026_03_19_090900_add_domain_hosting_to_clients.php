<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('domain_name')->nullable()->after('website');
            $table->date('domain_expiry')->nullable()->after('domain_name');
            $table->string('domain_provider')->nullable()->after('domain_expiry');
            $table->string('hosting_provider')->nullable()->after('domain_provider');
            $table->string('hosting_package')->nullable()->after('hosting_provider');
            $table->date('hosting_expiry')->nullable()->after('hosting_package');
            $table->text('hosting_notes')->nullable()->after('hosting_expiry');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'domain_name', 'domain_expiry', 'domain_provider',
                'hosting_provider', 'hosting_package', 'hosting_expiry', 'hosting_notes',
            ]);
        });
    }
};
