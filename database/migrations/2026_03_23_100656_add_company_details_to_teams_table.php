<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('company_phone', 50)->nullable()->after('slogan');
            $table->string('company_email', 150)->nullable()->after('company_phone');
            $table->text('company_address')->nullable()->after('company_email');
            $table->string('company_website', 255)->nullable()->after('company_address');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['company_phone', 'company_email', 'company_address', 'company_website']);
        });
    }
};
