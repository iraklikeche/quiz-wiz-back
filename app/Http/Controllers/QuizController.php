<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Http\Resources\QuizResource;
use App\Models\Category;
use App\Models\DifficultyLevel;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with(['difficultyLevel', 'categories', 'questions',])->paginate(6);
        return QuizResource::collection($quizzes);
    }

    public function show($id)
    {
        $quiz = Quiz::with(['difficultyLevel', 'categories', 'questions',])->findOrFail($id);
        return new QuizResource($quiz);

    }
    public function search(Request $request)
    {
        $query = Quiz::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%$search%");
        }

        $quizzes = $query->with(['difficultyLevel', 'categories', 'questions'])->get();
        return QuizResource::collection($quizzes);
    }


    public function initialData()
    {
        $categories = Category::all(['id', 'name']);
        $difficultyLevels = DifficultyLevel::all(['id', 'name', 'text_color', 'background_color']);

        return response()->json([
            'categories' => $categories,
            'difficultyLevels' => $difficultyLevels,
        ]);
    }

}
