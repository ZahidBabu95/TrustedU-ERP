<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negotiation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->dateTime('discussion_date');
            $table->string('discussion_type', 30)->default('phone'); // phone, email, meeting, video_call
            $table->text('summary');
            $table->decimal('counter_offer', 12, 2)->nullable();
            $table->text('module_changes')->nullable();
            $table->string('client_response', 30)->nullable(); // positive, neutral, negative
            $table->text('next_action')->nullable();
            $table->foreignId('logged_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('lead_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiation_logs');
    }
};
