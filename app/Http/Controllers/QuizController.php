<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\DifficultyLevelResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Http\Resources\QuizResource;
use App\Models\Category;
use App\Models\DifficultyLevel;

class QuizController extends Controller
{
    public function index(Request $request)
    {

        $query = Quiz::with(['difficultyLevel', 'categories', 'questions']);
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->has('categories')) {
            $categories = explode(',', $request->input('categories'));
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('categories.id', $categories);
            });

        }

        if ($request->has('difficulties')) {
            $difficulties = explode(',', $request->input('difficulties'));
            $query->whereIn('difficulty_level_id', $difficulties);
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'alphabet':
                    $query->orderBy('title');
                    break;
                case 'reverse-alphabet':
                    $query->orderByDesc('title');
                    break;
                    // I don't have user-quiz relation yet
                    // case 'most-popular':
                    //     // Assumes there's a way to measure popularity, e.g., a 'views' column
                    //     $query->orderByDesc('views');
                    //     break;
                case 'newest':
                    $query->orderByDesc('created_at');
                    break;
                case 'oldest':
                    $query->orderBy('created_at');
                    break;
            }
        }

        $quizzes = $query->paginate(6);
        return QuizResource::collection($quizzes);

    }
    public function show($id)
    {
        $quiz = Quiz::with(['difficultyLevel', 'categories', 'questions',])->findOrFail($id);
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



}
