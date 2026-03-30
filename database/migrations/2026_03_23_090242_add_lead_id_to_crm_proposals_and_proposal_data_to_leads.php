<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add lead_id to crm_proposals (optional, alongside deal_id)
        Schema::table('crm_proposals', function (Blueprint $table) {
            $table->foreignId('lead_id')->nullable()->after('id')->constrained('leads')->nullOnDelete();
        });

        // Add proposal_data JSON to leads for storing proposal details
        Schema::table('leads', function (Blueprint $table) {
            $table->json('proposal_data')->nullable()->after('source_details');
        });
    }

    public function down(): void
    {
        Schema::table('crm_proposals', function (Blueprint $table) {
            $table->dropForeign(['lead_id']);
            $table->dropColumn('lead_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('proposal_data');
        });
    }
};
