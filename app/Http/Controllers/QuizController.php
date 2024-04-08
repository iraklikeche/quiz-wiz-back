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

        $query = Quiz::with(['difficultyLevel', 'categories', 'questions'])
        ->search($request->input('search'))
        ->withCategories($request->input('categories'))
        ->withDifficulties($request->input('difficulties'))
        ->sortBy($request->input('sort'));

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
