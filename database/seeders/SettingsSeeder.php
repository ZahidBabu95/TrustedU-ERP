<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'label' => 'Site Name', 'value' => 'TrustEdu ERP', 'type' => 'text', 'group' => 'general'],
            ['key' => 'company_name', 'label' => 'Company Name', 'value' => 'Trust Innovation Limited (TIL)', 'type' => 'text', 'group' => 'general'],
            ['key' => 'army_managed_text', 'label' => 'Army Managed Text', 'value' => 'Managed by Bangladesh Army-operated Trust Innovation Limited', 'type' => 'textarea', 'group' => 'general'],
            
            // Stats for Hero
            ['key' => 'stats_institutions', 'label' => 'Total Institutions', 'value' => '63', 'type' => 'text', 'group' => 'stats'],
            ['key' => 'stats_success_institutions', 'label' => 'Success Institutions', 'value' => '17', 'type' => 'text', 'group' => 'stats'],

            // Contact
            ['key' => 'contact_address', 'label' => 'Address', 'value' => 'Trust Innovation Limited (TIL), Level 10, Trust Bank Tower, 25 Gulshan Avenue, Dhaka-1212', 'type' => 'textarea', 'group' => 'contact'],
            ['key' => 'contact_email', 'label' => 'Email', 'value' => 'info@tilbd.net', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_phone', 'label' => 'Phone', 'value' => '+880 1234 567890', 'type' => 'text', 'group' => 'contact'],
            
            // Links
            ['key' => 'company_website', 'label' => 'Company Website', 'value' => 'https://tilbd.net/', 'type' => 'text', 'group' => 'links'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
