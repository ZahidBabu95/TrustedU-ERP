<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_id', 20)->nullable()->unique()->after('id');
            $table->boolean('is_live')->default(false)->after('is_active');
        });

        // Generate client IDs for existing records
        $clients = DB::table('clients')->orderBy('id')->get();
        foreach ($clients as $index => $client) {
            DB::table('clients')
                ->where('id', $client->id)
                ->update(['client_id' => str_pad($client->id, 5, '0', STR_PAD_LEFT)]);
        }
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'is_live']);
        });
    }
};
