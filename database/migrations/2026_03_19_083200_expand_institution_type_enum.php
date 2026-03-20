<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change ENUM to include all institution types
        DB::statement("ALTER TABLE `clients` MODIFY COLUMN `institution_type` ENUM('school', 'college', 'school_and_college', 'university', 'madrasha', 'coaching', 'corporate', 'ngo', 'other') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `clients` MODIFY COLUMN `institution_type` ENUM('school', 'college', 'university', 'madrasha', 'other') NULL");
    }
};
