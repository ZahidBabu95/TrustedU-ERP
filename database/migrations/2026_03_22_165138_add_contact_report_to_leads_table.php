<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'contact_report')) {
                $table->json('contact_report')->nullable();
            }
            if (!Schema::hasColumn('leads', 'status_changed_at')) {
                $table->timestamp('status_changed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['contact_report', 'status_changed_at']);
        });
    }
};
