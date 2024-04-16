<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class AnswerQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Quiz::all() as $quiz) {
            Question::factory(rand(1, 0))->create()->each(function ($question) {
                Answer::factory(rand(1, 5))->create([
                   'question_id' => $question->id,
                ]);

                Answer::factory(1)->create([
                   'question_id' => $question->id,
                   'is_correct'       => 1,
                ]);
            });
        }
    }
}
