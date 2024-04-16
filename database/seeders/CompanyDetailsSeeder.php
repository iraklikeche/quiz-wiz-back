<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_details')->insert([
            'email' => 'quizwiz@gmail.com',
            'number' => '+995 328989',
            'facebook_link' => 'https://www.facebook.com/example',
            'linkedin' => 'https://www.linkedin.com/company/example',
        ]);
    }
}
