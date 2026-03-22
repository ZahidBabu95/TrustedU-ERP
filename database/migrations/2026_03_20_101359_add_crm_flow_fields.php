<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add demo_request_id to leads (team_id already exists)
        if (!Schema::hasColumn('leads', 'demo_request_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->foreignId('demo_request_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            });
        }

        // Add team_id to deals if not exists
        if (!Schema::hasColumn('deals', 'team_id')) {
            Schema::table('deals', function (Blueprint $table) {
                $table->foreignId('team_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        // Add lead_id and converted_at to demo_requests
        if (!Schema::hasColumn('demo_requests', 'lead_id')) {
            Schema::table('demo_requests', function (Blueprint $table) {
                $table->foreignId('lead_id')->nullable()->after('id')->constrained()->nullOnDelete();
                $table->timestamp('converted_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'demo_request_id')) {
                $table->dropForeign(['demo_request_id']);
                $table->dropColumn('demo_request_id');
            }
        });

        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'team_id')) {
                $table->dropForeign(['team_id']);
                $table->dropColumn('team_id');
            }
        });

        Schema::table('demo_requests', function (Blueprint $table) {
            if (Schema::hasColumn('demo_requests', 'lead_id')) {
                $table->dropForeign(['lead_id']);
                $table->dropColumn(['lead_id', 'converted_at']);
            }
        });
    }
};
