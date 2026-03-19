<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');            // general, sms, email, storage, payment, system, security
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->string('type')->default('text'); // text, textarea, select, toggle, file, password, number
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->json('options')->nullable();  // for select dropdowns
            $table->integer('sort_order')->default(0);
            $table->foreignId('company_id')->nullable(); // multi-company support (future)
            $table->timestamps();

            $table->index(['group', 'company_id']);
        });

        // Activity log for settings changes
        Schema::create('settings_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_audit_logs');
        Schema::dropIfExists('system_settings');
    }
};
