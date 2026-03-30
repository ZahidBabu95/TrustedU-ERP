<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->integer('version')->default(1);
            $table->string('title');
            $table->json('modules_included')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2)->default(0);
            $table->integer('implementation_days')->nullable();
            $table->text('payment_terms')->nullable();
            $table->integer('validity_days')->default(30);
            $table->string('status', 30)->default('draft');
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_disk', 50)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['deal_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_proposals');
    }
};
