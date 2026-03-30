<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `clients` MODIFY COLUMN `institution_type` ENUM('school', 'college', 'school_and_college', 'school_college', 'university', 'madrasha', 'coaching', 'coaching_center', 'corporate', 'ngo', 'other') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `clients` MODIFY COLUMN `institution_type` ENUM('school', 'college', 'school_and_college', 'university', 'madrasha', 'coaching', 'corporate', 'ngo', 'other') NULL");
    }
};
