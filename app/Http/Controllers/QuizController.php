<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['difficultyLevel', 'categories', 'questions',])->get();

        return response()->json($quizzes->map(function ($quiz) {
            return $this->transformQuiz($quiz);
        }));
    }

    public function show($id)
    {
        $quiz = Quiz::with(['difficultyLevel', 'categories', 'questions',])->findOrFail($id);

        return response()->json($this->transformQuiz($quiz));
    }

    private function transformQuiz($quiz)
    {
        $imageUrl = $quiz->image ? Storage::disk('public')->url($quiz->image) : null;

        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'image' => $imageUrl,
            'totalTime' => $quiz->estimated_time,
            'totalPoints' => $quiz->total_points,
            'numberOfQuestions' => $quiz->questions->count(),
            'questions' => $quiz->questions,
            'difficultyLevel' => [
                'name' => $quiz->difficultyLevel->name,
                'textColor' => $quiz->difficultyLevel->text_color ,
                'backgroundColor' => $quiz->difficultyLevel->background_color,
            ],
            'categories' => $quiz->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            }),
        ];
    }

}
