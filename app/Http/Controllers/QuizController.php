<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['difficultyLevel', 'categories'])->get();

        $quizzes = $quizzes->map(function ($quiz) {
            $imageUrl = $quiz->image ? Storage::disk('public')->url($quiz->image) : null;

            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'image' => $quiz->imageUrl,
                'totalTime' => $quiz->estimated_time,
            'totalPoints' => $quiz->total_points,
            'numberOfQuestions' => $quiz->questions->count(),
                'difficultyLevel' => [
                    'name' => $quiz->difficultyLevel->name,
                    'textColor' => $quiz->difficultyLevel->text_color,
                    'backgroundColor' => $quiz->difficultyLevel->background_color,
                ],
                'categories' => $quiz->categories
            ];
        });

        return response()->json($quizzes);
    }
}
