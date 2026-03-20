<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand stage enum to include discovery and contract
        DB::statement("ALTER TABLE deals MODIFY COLUMN stage ENUM('discovery','prospecting','proposal','negotiation','contract','closed_won','closed_lost') DEFAULT 'discovery'");

        Schema::table('deals', function (Blueprint $table) {
            $table->string('company')->nullable()->after('title');
            $table->string('contact_name')->nullable()->after('company');
            $table->string('contact_email')->nullable()->after('contact_name');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('stage');
            $table->string('label')->nullable()->after('priority');
            $table->enum('deal_source', ['lead', 'direct', 'referral', 'upsell', 'other'])->default('direct')->after('label');
            $table->integer('probability')->default(0)->after('deal_source');
            $table->integer('sort_order')->default(0)->after('probability');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn([
                'company', 'contact_name', 'contact_email', 'contact_phone',
                'priority', 'label', 'deal_source', 'probability', 'sort_order',
            ]);
        });

        DB::statement("ALTER TABLE deals MODIFY COLUMN stage ENUM('prospecting','proposal','negotiation','closed_won','closed_lost') DEFAULT 'prospecting'");
    }
};
