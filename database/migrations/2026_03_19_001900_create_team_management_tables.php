<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add department + designation columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('phone');
            $table->string('designation')->nullable()->after('department');
            $table->softDeletes();
        });

        // Employee Profiles (personal + professional info)
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Personal
            $table->string('profile_photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Professional
            $table->date('joining_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'intern', 'contract'])->default('full_time');
            $table->text('bio')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Employee Documents
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['cv', 'nid', 'certificate', 'contract', 'other'])->default('other');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable(); // in KB
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Employee Financials
        Schema::create('employee_financials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Bank Info
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('routing_number')->nullable();

            // Mobile Banking
            $table->enum('mobile_banking_type', ['bkash', 'nagad', 'rocket', 'upay', 'other'])->nullable();
            $table->string('mobile_banking_number')->nullable();

            // Salary (optional future)
            $table->decimal('base_salary', 12, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_financials');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_profiles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'designation']);
            $table->dropSoftDeletes();
        });
    }
};
