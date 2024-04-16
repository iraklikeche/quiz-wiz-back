<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class CategoryQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = Quiz::all();
        $categories = Category::factory(5)->create();

        $quizzes->each(function ($quiz) use ($categories) {
            $quiz->categories()->attach($categories->random(rand(1, $categories->count()))->pluck('id')->toArray());
        });
    }
}
