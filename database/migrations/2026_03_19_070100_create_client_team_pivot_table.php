<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create pivot table for client-team many-to-many
        Schema::create('client_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['client_id', 'team_id']);
        });

        // Migrate existing team_id data to pivot table
        $clients = DB::table('clients')->whereNotNull('team_id')->get();
        foreach ($clients as $client) {
            DB::table('client_team')->insert([
                'client_id'  => $client->id,
                'team_id'    => $client->team_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop old team_id column
        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
        });

        // Migrate back: take first team from pivot
        $pivots = DB::table('client_team')
            ->select('client_id', DB::raw('MIN(team_id) as team_id'))
            ->groupBy('client_id')
            ->get();

        foreach ($pivots as $pivot) {
            DB::table('clients')
                ->where('id', $pivot->client_id)
                ->update(['team_id' => $pivot->team_id]);
        }

        Schema::dropIfExists('client_team');
    }
};
