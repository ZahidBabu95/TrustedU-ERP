<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand status enum to include negotiation
        DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('new','contacted','qualified','proposal','negotiation','won','lost') DEFAULT 'new'");

        Schema::table('leads', function (Blueprint $table) {
            $table->string('company')->nullable()->after('name');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('source');
            $table->string('label')->nullable()->after('priority');
            $table->integer('sort_order')->default(0)->after('label');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['company', 'priority', 'label', 'sort_order']);
        });

        DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('new','contacted','qualified','proposal','won','lost') DEFAULT 'new'");
    }
};
