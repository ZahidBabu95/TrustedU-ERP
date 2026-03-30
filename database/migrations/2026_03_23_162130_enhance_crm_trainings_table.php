<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_trainings', function (Blueprint $table) {
            // Training category
            $table->string('training_category', 30)->default('migration')->after('training_type');
            // Session/meeting info
            $table->string('meeting_platform', 50)->nullable()->after('training_type');
            $table->string('meeting_link', 500)->nullable()->after('meeting_platform');
            $table->string('venue', 255)->nullable()->after('meeting_link');
            // Attendees
            $table->json('attendees')->nullable()->after('modules');
            $table->json('topics')->nullable()->after('attendees');
            // Session timing
            $table->time('session_time')->nullable()->after('start_date');
            $table->integer('session_duration_minutes')->default(60)->after('session_time');
            // Files
            $table->json('materials')->nullable()->after('feedback');
            $table->json('session_logs')->nullable()->after('materials');
            // Migration link
            $table->foreignId('migration_id')->nullable()->after('client_id')->constrained('crm_migrations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_trainings', function (Blueprint $table) {
            $table->dropColumn([
                'training_category', 'meeting_platform', 'meeting_link', 'venue',
                'attendees', 'topics', 'session_time', 'session_duration_minutes',
                'materials', 'session_logs', 'migration_id',
            ]);
        });
    }
};
