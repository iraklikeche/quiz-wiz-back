<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DifficultyLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('difficulty_levels')->insert(
            [
                [
                    'name' => 'Starter',
                    'background_color' => '#F0F9FF',
                    'text_color' => '#026AA2',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Beginner',
                    'background_color' => '#EFF8FF',
                    'text_color' => '#175CD3',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Middle',
                    'background_color' => '#F9F5FF',
                    'text_color' => '#6941C6',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'High',
                    'background_color' => '#FFFAEB',
                    'text_color' => '#B54708',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Very high',
                    'background_color' => '#FDF2FA',
                    'text_color' => '#C11574',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Dangerously high',
                    'background_color' => '#FFF1F3',
                    'text_color' => '#C01048',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
