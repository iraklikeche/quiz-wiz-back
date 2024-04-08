<?php

namespace App\Models;

use App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;
    protected $guarded = [];


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

    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where('title', 'like', "%{$term}%");
        }
    }

    public function scopeWithCategories($query, $categories)
    {
        if ($categories) {
            $categoriesArray = is_array($categories) ? $categories : explode(',', $categories);

            $query->whereHas('categories', function ($q) use ($categoriesArray) {
                $q->whereIn('categories.id', $categoriesArray);
            });
        }
    }

    public function scopeWithDifficulties($query, $difficulties)
    {
        if ($difficulties) {
            $difficultiesArray = is_array($difficulties) ? $difficulties : explode(',', $difficulties);

            $query->whereIn('difficulty_level_id', $difficultiesArray);
        }
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
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'oldest':
                $query->orderBy('created_at');
                break;
        }
    }

}
