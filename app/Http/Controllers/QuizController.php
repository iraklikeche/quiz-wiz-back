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
        $userId = auth()->id();
        $isMyQuizzes = auth()->check() && $request->input('my_quizzes') === 'true';
        $isNotCompleted = auth()->check() && $request->input('not_completed') === 'true';
        $query = Quiz::with(['difficultyLevel', 'categories', 'questions.answers', 'userAttempts'])
        ->search($request->input('search'))
        ->filterByCategories($request->input('categories'))
        ->filterByDifficulties($request->input('difficulties'))
        ->applyUserFilters($userId, $isMyQuizzes, $isNotCompleted)
        ->sortBy($request->input('sort'));

        $quizzes = $query->paginate(6);
        return QuizResource::collection($quizzes);

    }
    public function show($id)
    {
        $quiz = Quiz::with(['difficultyLevel', 'categories', 'questions.answers','userAttempts'])->findOrFail($id);
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
        $userId = auth()->id();

        $similarQuizzes = Quiz::similarToCategoriesAndNotCompleted($categoryIds, $excludeQuizId, $userId)->get();

        return QuizResource::collection($similarQuizzes);
    }


    public function submitAnswers(QuizSubmissionRequest $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $validated = $request->validated();
        $userId = auth()->id();
        if ($userId && $quiz->hasUserCompletedQuiz($userId)) {
            return response()->json(['message' => 'You have already completed this quiz.'], 403);
        }
        $questions = Question::withCorrectAnswersCount($validated['answers'])
                      ->whereIn('id', collect($validated['answers'])->pluck('questionId'))
                      ->get();
        $timeSpent = $validated['timeSpent'];

        $totalScore = $questions->sum('correct_answers_count');

        DB::table('quiz_user')->insert([
            'quiz_id' => $id,
            'user_id' => $userId,
            'score' => $totalScore,
            'time_spent' => $timeSpent,
            'created_at' => now()

        ]);

        return response()->json([
            'message' => 'Quiz answers submitted successfully.',
            'score' => $totalScore,
        ]);
    }

}
