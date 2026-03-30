<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('qualification_status')->default('pending')->after('stage');
            $table->text('qualification_notes')->nullable()->after('qualification_status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('qualification_notes');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->string('risk_level')->default('medium')->after('approved_at');
            $table->json('modules_required')->nullable()->after('risk_level');
            $table->boolean('previous_software_used')->default(false)->after('modules_required');
            $table->string('previous_software_name')->nullable()->after('previous_software_used');
            $table->text('pain_points')->nullable()->after('previous_software_name');
            $table->string('budget_range')->nullable()->after('pain_points');
            $table->string('decision_maker_name')->nullable()->after('budget_range');
            $table->string('decision_maker_role')->nullable()->after('decision_maker_name');
            $table->text('meeting_notes')->nullable()->after('decision_maker_role');
            $table->string('pipeline_stage')->default('proposal_draft')->after('stage');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'qualification_status', 'qualification_notes',
                'approved_by', 'approved_at', 'risk_level',
                'modules_required', 'previous_software_used',
                'previous_software_name', 'pain_points', 'budget_range',
                'decision_maker_name', 'decision_maker_role',
                'meeting_notes', 'pipeline_stage',
            ]);
        });
    }
};
