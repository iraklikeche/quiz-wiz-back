<?php

namespace Database\Factories;

use App\Models\DifficultyLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level_id = rand(1, DifficultyLevel::all()->count());
        return [
            'title' => fake()->sentence,
            'estimated_time' => fake()->numberBetween(3, 15),
            'instruction' => fake()->paragraph,
            'entry_question' => fake()->sentence,
            'difficulty_level_id' => $level_id,
            'image' => fake()->image(dir: 'public/storage', fullPath: false),

        ];
    }
}
