<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SimilarQuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_receives_similar_quizzes_not_completed()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $quizzesInCategory = Quiz::factory()->count(5)->create()->each(function ($quiz) use ($category) {
            $quiz->categories()->attach($category->id);
        });

        $quizzesInCategory->take(2)->each(function ($quiz) use ($user) {
            DB::table('quiz_user')->insert([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $this->actingAs($user);

        $response = $this->getJson(route('quizzes.similar', ['categoryIds' => $category->id]));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_guest_receives_random_similar_quizzes()
    {
        $category = Category::factory()->create();

        Quiz::factory()->count(5)->create()->each(function ($quiz) use ($category) {
            $quiz->categories()->attach($category->id);
        });

        $response = $this->getJson(route('quizzes.similar', ['categoryIds' => $category->id]));


        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }


}
