<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CloudStorageServiceProvider extends ServiceProvider
{
    /**
     * Boot: Read storage settings from DB and configure the r2/s3 disk dynamically.
     * Also set the default filesystem disk if configured.
     */
    public function boot(): void
    {
        // Skip if running in console (migration context) or table doesn't exist yet
        if ($this->app->runningInConsole() && !Schema::hasTable('system_settings')) {
            return;
        }

        try {
            if (!Schema::hasTable('system_settings')) {
                return;
            }

            $provider = \App\Models\SystemSetting::get('storage_provider', 'local');

            if (!in_array($provider, ['r2', 's3'])) {
                return; // Keep local
            }

            $accessKey = \App\Models\SystemSetting::get('storage_access_key');
            $secretKey = \App\Models\SystemSetting::get('storage_secret_key');
            $bucket    = \App\Models\SystemSetting::get('storage_bucket');
            $endpoint  = \App\Models\SystemSetting::get('storage_endpoint');
            $region    = \App\Models\SystemSetting::get('storage_region', 'auto');
            $publicUrl = \App\Models\SystemSetting::get('storage_public_url');
            $accountId = \App\Models\SystemSetting::get('storage_account_id');

            // For R2, auto-build endpoint from account ID if not explicitly set
            if ($provider === 'r2' && !$endpoint && $accountId) {
                $endpoint = "https://{$accountId}.r2.cloudflarestorage.com";
            }

            // Only configure if we have the required credentials
            if (!$accessKey || !$secretKey || !$bucket || !$endpoint) {
                return;
            }

            // Determine disk name
            $diskName = $provider === 'r2' ? 'r2' : 's3';

            // Configure the disk at runtime
            Config::set("filesystems.disks.{$diskName}", [
                'driver'                  => 's3',
                'key'                     => $accessKey,
                'secret'                  => $secretKey,
                'region'                  => $region,
                'bucket'                  => $bucket,
                'url'                     => $publicUrl,
                'endpoint'                => $endpoint,
                'use_path_style_endpoint' => false,
                'throw'                   => false,
                'report'                  => false,
            ]);

            // Set as default filesystem disk — all new uploads go here
            Config::set('filesystems.default', $diskName);

        } catch (\Exception $e) {
            // Silently fail — fall back to local storage
            report($e);
        }
    }
}
