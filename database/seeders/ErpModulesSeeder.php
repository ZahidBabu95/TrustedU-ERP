<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ErpModule;
use Illuminate\Support\Str;

class ErpModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing modules to avoid duplicates when running this again
        ErpModule::truncate();

        $modules = [
            [
                'name' => 'Student Registration',
                'icon' => 'heroicon-o-user-plus',
                'description' => 'Streamlined admission and robust student data management from pre-enrollment.',
                'features' => json_encode(['Pre-enrollment', 'Document Collection', 'Student ID Generation']),
                'color' => 'blue',
            ],
            [
                'name' => 'Student Information System (SIS)',
                'icon' => 'heroicon-o-users',
                'description' => 'Comprehensive academic and personal profiles of students.',
                'features' => json_encode(['Profile Management', 'Academic History', 'Parent Details']),
                'color' => 'indigo',
            ],
            [
                'name' => 'Student TC and Drop out',
                'icon' => 'heroicon-o-arrow-right-start-on-rectangle',
                'description' => 'Manage Transfer Certificates and Student Drop-out tracking systematically.',
                'features' => json_encode(['TC Generation', 'Drop-out Records', 'Clearance Management']),
                'color' => 'red',
            ],
            [
                'name' => 'Student Fees, Dues, Online Fee Collection',
                'icon' => 'heroicon-o-currency-dollar',
                'description' => 'Automate fee scheduling, invoicing, discounts, and online payment tracking.',
                'features' => json_encode(['Fee Scheduling', 'Online Payments', 'Due Tracking']),
                'color' => 'green',
            ],
            [
                'name' => 'Exam and Assessment Management',
                'icon' => 'heroicon-o-academic-cap',
                'description' => 'Generate schedules, admit cards, and process comprehensive exam results.',
                'features' => json_encode(['Exam Scheduling', 'Grading', 'Report Cards']),
                'color' => 'purple',
            ],
            [
                'name' => 'Attendance Tracking',
                'icon' => 'heroicon-o-clock',
                'description' => 'Biometric and manual daily attendance logging for students and staff.',
                'features' => json_encode(['Biometric Integration', 'Daily Logs', 'Absence Alerts']),
                'color' => 'teal',
            ],
            [
                'name' => 'SMS Module',
                'icon' => 'heroicon-o-chat-bubble-left-ellipses',
                'description' => 'Instant alerts for attendance, fees, result publications, and notices.',
                'features' => json_encode(['Bulk SMS', 'Automated Alerts', 'Notice Broadcast']),
                'color' => 'orange',
            ],
            [
                'name' => 'Transport management',
                'icon' => 'heroicon-o-truck',
                'description' => 'Route planning, fleet management, and real-time tracking.',
                'features' => json_encode(['Route Planning', 'Vehicle Tracking', 'Driver Details']),
                'color' => 'yellow',
            ],
            [
                'name' => 'Mobile App for Teacher and Student',
                'icon' => 'heroicon-o-device-phone-mobile',
                'description' => 'Dedicated Android/iOS apps for better engagement on the go.',
                'features' => json_encode(['Push Notifications', 'Homework App', 'Routine Access']),
                'color' => 'blue',
            ],
            [
                'name' => 'User, Role, Access Control',
                'icon' => 'heroicon-o-shield-check',
                'description' => 'Granular role definitions to secure system data and workflows.',
                'features' => json_encode(['RBAC', 'Permission Settings', 'Data Security']),
                'color' => 'slate',
            ],
            [
                'name' => 'Dynamic website',
                'icon' => 'heroicon-o-globe-alt',
                'description' => 'An automated, SEO optimized school website synced with ERP data.',
                'features' => json_encode(['CMS Builder', 'Notice Board', 'Event Gallery']),
                'color' => 'cyan',
            ],
            [
                'name' => 'Online Admission/ Admission Management',
                'icon' => 'heroicon-o-document-text',
                'description' => 'Collect applications digitally, take online exams, and publish merit lists.',
                'features' => json_encode(['Application Forms', 'Merit Lists', 'Admission Tests']),
                'color' => 'emerald',
            ],
            [
                'name' => 'Transport Tracking',
                'icon' => 'heroicon-o-map-pin',
                'description' => 'GPS-integrated tracking of school buses for student safety.',
                'features' => json_encode(['GPS Tracking', 'Parent Alerts', 'Stoppage Alarms']),
                'color' => 'red',
            ],
            [
                'name' => 'HRM, Leave & Payroll',
                'icon' => 'heroicon-o-briefcase',
                'description' => 'Staff profiles, leave approval system, and automated salary slip generation.',
                'features' => json_encode(['Payroll Generation', 'Leave Management', 'Staff Appraisals']),
                'color' => 'indigo',
            ],
            [
                'name' => 'Financial/ Accounts Management',
                'icon' => 'heroicon-o-banknotes',
                'description' => 'Daily ledgers, vouchers, and full accounting flow.',
                'features' => json_encode(['Ledger Management', 'Vouchers', 'Expense Tracking']),
                'color' => 'green',
            ],
            [
                'name' => 'Learning Management System (LMS)',
                'icon' => 'heroicon-o-book-open',
                'description' => 'E-learning platform for digital notes, interactive classes, and assignments.',
                'features' => json_encode(['Video Lectures', 'Assignments', 'Study Materials']),
                'color' => 'violet',
            ],
            [
                'name' => 'Assets and Inventory Management',
                'icon' => 'heroicon-o-archive-box',
                'description' => 'Track institutional properties, lab equipment, and stationary stocks.',
                'features' => json_encode(['Stock Ledger', 'Asset Tracking', 'Vendor Management']),
                'color' => 'orange',
            ],
            [
                'name' => 'Library Management',
                'icon' => 'heroicon-o-building-library',
                'description' => 'Digital book catalogs, issue/return logs, and fine calculation.',
                'features' => json_encode(['Barcode Scanning', 'Issue/Return', 'Digital Catalog']),
                'color' => 'fuchsia',
            ],
            [
                'name' => 'Hostel Management',
                'icon' => 'heroicon-o-home-modern',
                'description' => 'Room allocations, hostel fees, and mess management for students.',
                'features' => json_encode(['Room Allocation', 'Mess Bills', 'Visitor Logs']),
                'color' => 'rose',
            ],
            [
                'name' => 'OMR',
                'icon' => 'heroicon-o-qr-code',
                'description' => 'Automated checking of MCQ answers through optical mark recognition.',
                'features' => json_encode(['Sheet Scanning', 'Auto Marking', 'Result Sync']),
                'color' => 'slate',
            ],
        ];

        $order = 1;
        foreach ($modules as $module) {
            ErpModule::create([
                'name' => $module['name'],
                'slug' => Str::slug($module['name']),
                'icon' => $module['icon'],
                'description' => $module['description'],
                'features' => json_decode($module['features'], true),
                'color' => $module['color'],
                'sort_order' => $order++,
                'is_active' => true,
            ]);
        }
    }
}
