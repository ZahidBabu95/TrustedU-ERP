<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'group', 'key', 'value', 'is_encrypted', 'type',
        'label', 'description', 'options', 'sort_order', 'company_id',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'options'       => 'array',
    ];

    // ── Cache key ──
    private static string $cacheKey = 'system_settings_all';
    private static int $cacheTtl = 3600; // 1 hour

    /**
     * Get a setting value by key.
     * Uses cache for performance, decrypts sensitive values.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::getAllCached();
        $setting = $settings->firstWhere('key', $key);

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        // Decrypt if encrypted
        if ($setting->is_encrypted && $value) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $default;
            }
        }

        // Cast booleans
        if ($setting->type === 'toggle') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return $value ?? $default;
    }

    /**
     * Set a setting value.
     * Encrypts sensitive values, clears cache, logs change.
     */
    public static function set(string $key, mixed $value, ?int $companyId = null): void
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return;
        }

        $oldValue = $setting->value;
        $newValue = $value;

        // Encrypt if flagged
        if ($setting->is_encrypted && $newValue !== null && $newValue !== '') {
            $newValue = Crypt::encryptString((string) $newValue);
        }

        $setting->update(['value' => $newValue]);

        // Clear cache
        static::clearCache();

        // Log the change
        static::logChange($key, $oldValue, $newValue);
    }

    /**
     * Get all settings from cache.
     */
    public static function getAllCached()
    {
        return Cache::remember(static::$cacheKey, static::$cacheTtl, function () {
            return static::orderBy('group')->orderBy('sort_order')->get();
        });
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        $settings = static::getAllCached()->where('group', $group);
        $result = [];

        foreach ($settings as $setting) {
            $value = $setting->value;
            if ($setting->is_encrypted && $value) {
                try {
                    $value = Crypt::decryptString($value);
                } catch (\Exception $e) {
                    $value = null;
                }
            }
            $result[$setting->key] = $value;
        }

        return $result;
    }

    /**
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(static::$cacheKey);
    }

    /**
     * Log setting change.
     */
    private static function logChange(string $key, ?string $oldValue, ?string $newValue): void
    {
        \Illuminate\Support\Facades\DB::table('settings_audit_logs')->insert([
            'key'        => $key,
            'old_value'  => $oldValue,
            'new_value'  => $newValue,
            'user_id'    => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Public wrapper for logChange (used by Settings Page).
     */
    public static function logChangePublic(string $key, ?string $oldValue, ?string $newValue): void
    {
        static::logChange($key, $oldValue, $newValue);
    }

    /**
     * Seed default settings.
     */
    public static function seedDefaults(): void
    {
        $defaults = [
            // ── General ──
            ['group' => 'general', 'key' => 'app_name', 'label' => 'Application Name', 'value' => 'TrustedU ERP', 'type' => 'text', 'sort_order' => 1],
            ['group' => 'general', 'key' => 'company_name', 'label' => 'Company Name', 'value' => 'TrustedU', 'type' => 'text', 'sort_order' => 2],
            ['group' => 'general', 'key' => 'company_logo', 'label' => 'Company Logo', 'value' => null, 'type' => 'file', 'sort_order' => 3],
            ['group' => 'general', 'key' => 'favicon', 'label' => 'Favicon', 'value' => null, 'type' => 'file', 'sort_order' => 4],
            ['group' => 'general', 'key' => 'timezone', 'label' => 'Timezone', 'value' => 'Asia/Dhaka', 'type' => 'select', 'sort_order' => 5, 'options' => json_encode(['Asia/Dhaka', 'UTC', 'America/New_York', 'Europe/London', 'Asia/Kolkata'])],
            ['group' => 'general', 'key' => 'currency', 'label' => 'Currency', 'value' => 'BDT', 'type' => 'select', 'sort_order' => 6, 'options' => json_encode(['BDT', 'USD', 'EUR', 'GBP', 'INR'])],
            ['group' => 'general', 'key' => 'date_format', 'label' => 'Date Format', 'value' => 'd M, Y', 'type' => 'select', 'sort_order' => 7, 'options' => json_encode(['d M, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y'])],

            // ── SMS ──
            ['group' => 'sms', 'key' => 'sms_provider', 'label' => 'SMS Provider', 'value' => null, 'type' => 'select', 'sort_order' => 1, 'options' => json_encode(['twilio', 'bulksms', 'custom'])],
            ['group' => 'sms', 'key' => 'sms_api_key', 'label' => 'API Key', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 2],
            ['group' => 'sms', 'key' => 'sms_api_secret', 'label' => 'API Secret', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 3],
            ['group' => 'sms', 'key' => 'sms_sender_id', 'label' => 'Sender ID', 'value' => null, 'type' => 'text', 'sort_order' => 4],
            ['group' => 'sms', 'key' => 'sms_enabled', 'label' => 'SMS Enabled', 'value' => '0', 'type' => 'toggle', 'sort_order' => 5],

            // ── Email ──
            ['group' => 'email', 'key' => 'mail_driver', 'label' => 'Mail Driver', 'value' => 'smtp', 'type' => 'select', 'sort_order' => 1, 'options' => json_encode(['smtp', 'sendmail', 'mailgun', 'ses', 'postmark'])],
            ['group' => 'email', 'key' => 'mail_host', 'label' => 'Mail Host', 'value' => null, 'type' => 'text', 'sort_order' => 2],
            ['group' => 'email', 'key' => 'mail_port', 'label' => 'Mail Port', 'value' => '587', 'type' => 'number', 'sort_order' => 3],
            ['group' => 'email', 'key' => 'mail_username', 'label' => 'Mail Username', 'value' => null, 'type' => 'text', 'sort_order' => 4],
            ['group' => 'email', 'key' => 'mail_password', 'label' => 'Mail Password', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 5],
            ['group' => 'email', 'key' => 'mail_encryption', 'label' => 'Encryption', 'value' => 'tls', 'type' => 'select', 'sort_order' => 6, 'options' => json_encode(['tls', 'ssl', 'none'])],
            ['group' => 'email', 'key' => 'mail_from_address', 'label' => 'From Address', 'value' => null, 'type' => 'text', 'sort_order' => 7],
            ['group' => 'email', 'key' => 'mail_from_name', 'label' => 'From Name', 'value' => 'TrustedU ERP', 'type' => 'text', 'sort_order' => 8],

            // ── Storage ──
            ['group' => 'storage', 'key' => 'storage_provider', 'label' => 'Storage Provider', 'value' => 'local', 'type' => 'select', 'sort_order' => 1, 'options' => json_encode(['local', 's3', 'r2'])],
            ['group' => 'storage', 'key' => 'storage_account_id', 'label' => 'Account ID', 'value' => null, 'type' => 'text', 'sort_order' => 2],
            ['group' => 'storage', 'key' => 'storage_access_key', 'label' => 'Access Key', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 3],
            ['group' => 'storage', 'key' => 'storage_secret_key', 'label' => 'Secret Key', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 4],
            ['group' => 'storage', 'key' => 'storage_bucket', 'label' => 'Bucket Name', 'value' => null, 'type' => 'text', 'sort_order' => 5],
            ['group' => 'storage', 'key' => 'storage_endpoint', 'label' => 'Endpoint (R2/S3)', 'value' => null, 'type' => 'text', 'sort_order' => 6],
            ['group' => 'storage', 'key' => 'storage_region', 'label' => 'Region', 'value' => 'auto', 'type' => 'text', 'sort_order' => 7],
            ['group' => 'storage', 'key' => 'storage_public_url', 'label' => 'Public URL (CDN)', 'value' => null, 'type' => 'text', 'sort_order' => 8],

            // ── Payment ──
            ['group' => 'payment', 'key' => 'payment_gateway', 'label' => 'Payment Gateway', 'value' => null, 'type' => 'select', 'sort_order' => 1, 'options' => json_encode(['stripe', 'sslcommerz', 'paypal', 'razorpay'])],
            ['group' => 'payment', 'key' => 'payment_api_key', 'label' => 'API Key', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 2],
            ['group' => 'payment', 'key' => 'payment_api_secret', 'label' => 'API Secret', 'value' => null, 'type' => 'password', 'is_encrypted' => true, 'sort_order' => 3],
            ['group' => 'payment', 'key' => 'payment_sandbox', 'label' => 'Sandbox Mode', 'value' => '1', 'type' => 'toggle', 'sort_order' => 4],

            // ── System ──
            ['group' => 'system', 'key' => 'maintenance_mode', 'label' => 'Maintenance Mode', 'value' => '0', 'type' => 'toggle', 'sort_order' => 1],
            ['group' => 'system', 'key' => 'registration_enabled', 'label' => 'User Registration', 'value' => '1', 'type' => 'toggle', 'sort_order' => 2],
            ['group' => 'system', 'key' => 'default_user_role', 'label' => 'Default User Role', 'value' => 'viewer', 'type' => 'select', 'sort_order' => 3, 'options' => json_encode(['super_admin', 'admin', 'editor', 'sales', 'team_member', 'viewer'])],
            ['group' => 'system', 'key' => 'items_per_page', 'label' => 'Items Per Page', 'value' => '25', 'type' => 'number', 'sort_order' => 4],
            ['group' => 'system', 'key' => 'enable_activity_log', 'label' => 'Activity Log', 'value' => '1', 'type' => 'toggle', 'sort_order' => 5],

            // ── Security ──
            ['group' => 'security', 'key' => 'password_min_length', 'label' => 'Min Password Length', 'value' => '8', 'type' => 'number', 'sort_order' => 1],
            ['group' => 'security', 'key' => 'password_require_uppercase', 'label' => 'Require Uppercase', 'value' => '1', 'type' => 'toggle', 'sort_order' => 2],
            ['group' => 'security', 'key' => 'password_require_numbers', 'label' => 'Require Numbers', 'value' => '1', 'type' => 'toggle', 'sort_order' => 3],
            ['group' => 'security', 'key' => 'password_require_symbols', 'label' => 'Require Symbols', 'value' => '0', 'type' => 'toggle', 'sort_order' => 4],
            ['group' => 'security', 'key' => 'session_timeout', 'label' => 'Session Timeout (min)', 'value' => '120', 'type' => 'number', 'sort_order' => 5],
            ['group' => 'security', 'key' => 'max_login_attempts', 'label' => 'Max Login Attempts', 'value' => '5', 'type' => 'number', 'sort_order' => 6],
            ['group' => 'security', 'key' => 'two_factor_enabled', 'label' => '2FA Enabled', 'value' => '0', 'type' => 'toggle', 'sort_order' => 7],
            ['group' => 'security', 'key' => 'force_ssl', 'label' => 'Force SSL', 'value' => '0', 'type' => 'toggle', 'sort_order' => 8],

            // ── Integrations (Google Analytics) ──
            ['group' => 'integrations', 'key' => 'ga_enabled', 'label' => 'Enable Google Analytics', 'value' => '0', 'type' => 'toggle', 'sort_order' => 1],
            ['group' => 'integrations', 'key' => 'ga_measurement_id', 'label' => 'Measurement ID', 'value' => null, 'type' => 'text', 'sort_order' => 2],
            ['group' => 'integrations', 'key' => 'ga_track_events', 'label' => 'Track Custom Events', 'value' => '1', 'type' => 'toggle', 'sort_order' => 3],
        ];

        foreach ($defaults as $setting) {
            static::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
