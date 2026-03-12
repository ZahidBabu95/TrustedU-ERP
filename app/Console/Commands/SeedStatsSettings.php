<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class SeedStatsSettings extends Command
{
    protected $signature   = 'stats:seed';
    protected $description = 'Insert missing platform stats settings into the DB';

    public function handle(): void
    {
        $items = [
            ['key' => 'stats_students',        'label' => 'Total Students',    'value' => '50000', 'group' => 'stats', 'type' => 'number'],
            ['key' => 'stats_active_campuses',  'label' => 'Active Campuses',   'value' => '17',    'group' => 'stats', 'type' => 'number'],
            ['key' => 'stats_modules',          'label' => 'Total ERP Modules', 'value' => '18',    'group' => 'stats', 'type' => 'number'],
        ];

        foreach ($items as $item) {
            $created = Setting::firstOrCreate(['key' => $item['key']], $item);
            $this->line(($created->wasRecentlyCreated ? '  <info>✓ Created</info>' : '  <comment>→ Exists </comment>') . '  ' . $item['key']);
        }

        $this->info('Done!');
    }
}
