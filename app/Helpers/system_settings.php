<?php

use App\Models\SystemSetting;

if (!function_exists('system_setting')) {
    /**
     * Get or set a system setting value.
     *
     * Usage:
     *   system_setting('app_name')                    // Get value
     *   system_setting('app_name', 'My App')           // Get with default
     *   system_setting(['app_name' => 'New Name'])     // Set value
     */
    function system_setting(string|array|null $key = null, mixed $default = null): mixed
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                SystemSetting::set($k, $v);
            }
            return true;
        }

        return SystemSetting::get($key, $default);
    }
}
