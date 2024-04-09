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

    public function similarQuizzes($id)
    {
        $quiz = Quiz::findOrFail($id);
        $categoryIds = $quiz->categories->pluck('id');

        $similarQuizzes = Quiz::similarTo($id, $categoryIds)->get();

        return QuizResource::collection($similarQuizzes);

    }

}
