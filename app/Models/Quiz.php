<?php

namespace App\Models;

use App\Http\Resources\QuizResource;
use App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Quiz extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['totalAttempts'];




    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function difficultyLevel()
    {
        return $this->belongsTo(DifficultyLevel::class);
    }
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('title', 'like', "%{$term}%");
        }
    }


    public function scopeSimilarToCategories($query, $categoryIds, $excludeQuizId = null)
    {

        return $query->when($excludeQuizId, function ($query) use ($excludeQuizId) {
            $query->where('id', '!=', $excludeQuizId);
        })
        ->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        });

    }
    public function scopeSimilarToCategoriesAndNotCompleted($query, $categoryIds, $excludeQuizId = null, $userId)
    {
        $this->scopeSimilarToCategories($query, $categoryIds, $excludeQuizId);


        return $query->whereDoesntHave('userAttempts', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with(['categories', 'questions.answers', 'difficultyLevel'])
        ->take(3);
    }

    public function scopeApplyUserFilters($query, $userId, $isMyQuizzes, $isNotCompleted)
    {
        if ($isMyQuizzes && $isNotCompleted) {

        } else {
            $query->when($isMyQuizzes && !$isNotCompleted, function ($q) use ($userId) {
                return $q->myQuizzes($userId);
            });

            $query->when($isNotCompleted && !$isMyQuizzes, function ($q) use ($userId) {
                return $q->notCompletedQuizzes($userId);
            });
        }
    }


    public function hasUserCompletedQuiz($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    public function userAttempts()
    {
        return $this->belongsToMany(User::class, 'quiz_user')
        ->withPivot('score', 'time_spent', 'created_at')
        ->withTimestamps();
    }


    public function scopeFilterByCategories($query, $categories)
    {
        if ($categories) {
            $categoriesArray = is_array($categories) ? $categories : explode(',', $categories);
            $query->whereHas('categories', function ($q) use ($categoriesArray) {
                $q->whereIn('categories.id', $categoriesArray);
            });
        }
    }

    public function scopeFilterByDifficulties($query, $difficulties)
    {
        if ($difficulties) {
            $difficultiesArray = is_array($difficulties) ? $difficulties : explode(',', $difficulties);
            $query->whereIn('difficulty_level_id', $difficultiesArray);
        }
    }

    public function scopeMyQuizzes($query, $userId)
    {
        return $query->whereHas('userAttempts', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeNotCompletedQuizzes($query, $userId)
    {
        return $query->whereDoesntHave('userAttempts', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeSortBy($query, $criteria)
    {
        switch ($criteria) {
            case 'alphabet':
                $query->orderBy('title');
                break;
            case 'reverse-alphabet':
                $query->orderByDesc('title');
                break;
            case 'popular':
                $query->select('quizzes.*', DB::raw('COUNT(quiz_user.quiz_id) as attempts_count'))
                      ->leftJoin('quiz_user', 'quizzes.id', '=', 'quiz_user.quiz_id')
                      ->groupBy('quizzes.id')
                      ->orderByDesc('attempts_count');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'oldest':
                $query->orderBy('created_at');
                break;
        }
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function getTotalAttemptsAttribute()
    {
        return DB::table('quiz_user')->where('quiz_id', $this->id)->count();
    }


}
