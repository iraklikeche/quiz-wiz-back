<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuizSubmissionRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DifficultyLevelResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Http\Resources\QuizResource;
use App\Models\Category;
use App\Models\DifficultyLevel;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with(['difficultyLevel', 'categories', 'questions.answers'])
        ->search($request->input('search'))
        ->filterByCategories($request->input('categories'))
        ->filterByDifficulties($request->input('difficulties'))
        ->sortBy($request->input('sort'));

        $quizzes = $query->paginate(6);
        return QuizResource::collection($quizzes);

    }
    public function show($id)
    {
        $quiz = Quiz::with(['difficultyLevel', 'categories', 'questions.answers',])->findOrFail($id);
        return new QuizResource($quiz);
    }


    public function getAllCategories()
    {
        $categories = Category::all(['id', 'name']);
        return CategoryResource::collection($categories);
    }

    public function getAllDifficultyLevels()
    {
        $difficultyLevels = DifficultyLevel::all(['id', 'name', 'text_color', 'background_color']);
        return DifficultyLevelResource::collection($difficultyLevels);
    }


    public function similarQuizzesByCategories(Request $request)
    {
        $categoryIds = explode(',', $request->query('categoryIds'));
        $excludeQuizId = $request->query('excludeQuizId');

        $similarQuizzes = Quiz::similarToCategories($categoryIds, $excludeQuizId)->get();

        return QuizResource::collection($similarQuizzes);
    }


    public function submitAnswers(QuizSubmissionRequest $request, $id)
    {
        $quiz = Quiz::findOrFail($id);


        $validated = $request->validated();


        $score = 0;
        foreach ($validated['answers'] as $answer) {
            $question = Question::with('answers')->findOrFail($answer['questionId']);
            foreach ($answer['selectedAnswerIds'] as $selectedAnswerId) {
                $correctAnswer = $question->answers->firstWhere('id', $selectedAnswerId);
                if ($correctAnswer && $correctAnswer->is_correct) {
                    $score++;
                }
            }
        }

        $userId = auth()->check() ? auth()->id() : null;

        $attempt = [
            'quiz_id' => $id,
            'user_id' => $userId,
            'score' => $score,
            'time_spent' => $validated['timeSpent'],
            'completed_at' => now(),
        ];

        DB::table('quiz_user')->insert($attempt);

        return response()->json([
            'message' => 'Quiz answers submitted successfully.',
            'score' => $score,
        ]);
    }

}
