<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientsSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            'Shaheed Bir Uttam Lt. Anwar Girls\' School & College',
            'Adamjee Cantonment Public School and College, Jolshiri',
            'Bangladesh International School and College, Jolshiri',
            'Baridhara Scholars\' International School and College (BSISC)',
            'Barishal Cantonment Public School & College',
            'CANTONMENT PUBLIC SCHOOL & COLLEGE, SAIDPUR',
            'Padma Cantonment Public School & College',
            'Jamuna Cantonment Public School and College, Tangail',
            'Jolshiri Cantonment School and College',
            'Dhaka Cantonment Girls\' Public School and College',
            'Cantonment Public School And College Lalmonirhat',
            'Jashore English School & College (JESC)',
            'Cantonment Public School and College Momenshahi (CPSCM)',
            'Ramu Cantonment English School & College',
            'Cantonment Board Secondary School',
            'Ghatail Cantonment English School And College',
            'Cantonment High School Jessore'
        ];

        foreach ($clients as $index => $name) {
            Client::updateOrCreate(
                ['name' => $name],
                [
                    'logo' => null, // Will be updated later via dashboard
                    'is_active' => true,
                    'sort_order' => $index + 1
                ]
            );
        }
    }
}
