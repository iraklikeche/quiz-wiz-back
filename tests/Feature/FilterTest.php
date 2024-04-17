<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DifficultyLevel;
use App\Models\Quiz;
use App\Models\User;
use Database\Seeders\CategoryQuizSeeder;
use Database\Seeders\DifficultyLevelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DifficultyLevelSeeder::class);
        $this->seed(CategoryQuizSeeder::class);
    }
    public function test_retrieves_quizzes_without_filters()
    {
        Quiz::factory()->count(5)->create();

        $response = $this->getJson(route('quizzes.index'));

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_retrieves_quizzes_filtered_by_categories()
    {
        $category = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        Quiz::factory()->count(2)->create()->each(function ($quiz) use ($category) {
            $quiz->categories()->attach($category->id);
        });
        Quiz::factory()->count(3)->create()->each(function ($quiz) use ($otherCategory) {
            $quiz->categories()->attach($otherCategory->id);
        });

        $response = $this->getJson(route('quizzes.index', ['categories' => $category->id]));

        $response->assertOk();
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_retrieves_quizzes_filtered_by_difficulty()
    {
        $difficulty = DifficultyLevel::where('name', '=', 'Starter')->firstOrFail();

        Quiz::factory()->count(2)->create(['difficulty_level_id' => $difficulty->id]);

        $response = $this->getJson(route('quizzes.index', ['difficulties' => $difficulty->id]));

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_retrieves_quizzes_sorted_alphabetically()
    {
        Quiz::factory()->create(['title' => 'C Quiz']);
        Quiz::factory()->create(['title' => 'A Quiz']);
        Quiz::factory()->create(['title' => 'B Quiz']);


        $response = $this->getJson(route('quizzes.index', ['sort' => 'alphabet']));


        $response->assertOk();
        $titles = array_column($response->json('data'), 'title');
        $this->assertEquals(['A Quiz', 'B Quiz', 'C Quiz'], $titles);
    }

    public function test_retrieves_quizzes_sorted_reverse_alphabetically()
    {
        Quiz::factory()->create(['title' => 'C Quiz']);
        Quiz::factory()->create(['title' => 'A Quiz']);
        Quiz::factory()->create(['title' => 'B Quiz']);

        $response = $this->getJson(route('quizzes.index', ['sort' => 'reverse-alphabet']));



        $response->assertOk();
        $titles = array_column($response->json('data'), 'title');
        $this->assertEquals(['C Quiz', 'B Quiz', 'A Quiz'], $titles);
    }


    public function test_retrieves_quizzes_sorted_by_newest()
    {
        $quiz1 = Quiz::factory()->create(['created_at' => now()->subDays(2)]);
        $quiz2 = Quiz::factory()->create(['created_at' => now()->subDays(1)]);
        $quiz3 = Quiz::factory()->create(['created_at' => now()]);

        $response = $this->getJson(route('quizzes.index', ['sort' => 'newest']));


        $response->assertOk();

        $quizIds = array_column($response->json('data'), 'id');

        $expectedOrder = [$quiz3->id, $quiz2->id, $quiz1->id];
        $this->assertEquals($expectedOrder, $quizIds);
    }

    public function test_retrieves_quizzes_sorted_by_oldest()
    {
        $quiz1 = Quiz::factory()->create(['created_at' => now()->subDays(2)]);
        $quiz2 = Quiz::factory()->create(['created_at' => now()->subDays(1)]);
        $quiz3 = Quiz::factory()->create(['created_at' => now()]);

        $response = $this->getJson(route('quizzes.index', ['sort' => 'oldest']));


        $response->assertOk();

        $quizIds = array_column($response->json('data'), 'id');

        $expectedOrder = [$quiz1->id, $quiz2->id, $quiz3->id];
        $this->assertEquals($expectedOrder, $quizIds);
    }


    public function test_retrieves_quizzes_sorted_by_popularity()
    {
        $quiz1 = Quiz::factory()->create();
        $quiz2 = Quiz::factory()->create();
        $quiz3 = Quiz::factory()->create();

        $usersForQuiz1 = User::factory()->count(5)->create()->each(function ($user) use ($quiz1) {
            DB::table('quiz_user')->insert([
                'quiz_id' => $quiz1->id,
                'user_id' => $user->id,
                'score' => rand(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $usersForQuiz2 = User::factory()->count(10)->create()->each(function ($user) use ($quiz2) {
            DB::table('quiz_user')->insert([
                'quiz_id' => $quiz2->id,
                'user_id' => $user->id,
                'score' => rand(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });


        $response = $this->getJson(route('quizzes.index', ['sort' => 'popular']));

        $response->assertOk();

        $quizIds = array_column($response->json('data'), 'id');

        $expectedOrder = [$quiz2->id, $quiz1->id, $quiz3->id];
        $this->assertEquals($expectedOrder, $quizIds);
    }

    public function test_authenticated_user_can_filter_my_quizzes()
    {
        $user = User::factory()->create();
        $attemptedQuizzes = Quiz::factory()->count(2)->create()->each(function ($quiz) use ($user) {
            DB::table('quiz_user')->insert([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Quiz::factory()->count(3)->create();

        $this->actingAs($user);

        $response = $this->getJson(route('quizzes.index', ['my_quizzes' => 'true']));


        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }



    public function test_authenticated_user_can_filter_not_completed_quizzes()
    {
        $user = User::factory()->create();
        $attemptedQuizzes = Quiz::factory()->count(2)->create()->each(function ($quiz) use ($user) {
            DB::table('quiz_user')->insert([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $notAttemptedQuizzes = Quiz::factory()->count(3)->create();

        $this->actingAs($user);

        $response = $this->getJson(route('quizzes.index', ['not_completed' => 'true']));


        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }
}
