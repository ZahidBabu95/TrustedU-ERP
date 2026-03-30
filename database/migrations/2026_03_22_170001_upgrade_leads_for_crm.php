<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('institute_name')->nullable()->after('company');
            $table->string('institute_type')->nullable()->after('institute_name');
            $table->integer('student_count')->nullable()->after('institute_type');
            $table->string('contact_person')->nullable()->after('student_count');
            $table->text('address')->nullable()->after('contact_person');
            $table->string('interest_level')->default('warm')->after('priority');
            $table->date('follow_up_date')->nullable()->after('expected_close_date');
            $table->text('follow_up_notes')->nullable()->after('follow_up_date');
            $table->integer('qualification_score')->nullable()->after('follow_up_notes');
            $table->string('lost_reason')->nullable()->after('qualification_score');
            $table->timestamp('lost_at')->nullable()->after('lost_reason');
            $table->string('pipeline_stage')->default('new_lead')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'institute_name', 'institute_type', 'student_count',
                'contact_person', 'address', 'interest_level',
                'follow_up_date', 'follow_up_notes', 'qualification_score',
                'lost_reason', 'lost_at', 'pipeline_stage',
            ]);
        });
    }
};
