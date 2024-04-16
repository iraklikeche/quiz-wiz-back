<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class QuizUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = Quiz::factory(5)->create();

        $user = User::factory()->create([
            'username'     => 'Test',
            'email'        => 'test@test.com',
            'password'     => 'test',
        ]);
        $user->quizzes()->attach(
            $quizzes->random(rand(1, $quizzes->count()))->pluck('id')->toArray(),
            [
               'created_at' => Carbon::now(),
               'time_spent'         => rand(0, 10),
               'score'        => rand(0, 10),
            ]
        );

        $users = User::factory(4)->create();
        $users->each(function ($user) use ($quizzes) {
            $user->quizzes()->attach(
                $quizzes->random(rand(1, $quizzes->count()))->pluck('id')->toArray(),
                [
                  'created_at' => Carbon::now(),
                  'time_spent'         => rand(0, 10),
                  'score'        => rand(0, 10),
               ]
            );
        });
    }
}
