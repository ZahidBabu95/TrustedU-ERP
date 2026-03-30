<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->string('client_name');
            $table->string('institute_name')->nullable();
            $table->json('items')->nullable(); // module list with pricing
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('expected_delivery')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('deliverables')->nullable();
            $table->string('status', 30)->default('generated'); // generated, in_progress, completed
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
