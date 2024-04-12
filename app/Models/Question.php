<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function scopeWithCorrectAnswersCount($query, $validatedAnswers)
    {
        return $query->withCount(['answers as correct_answers_count' => function ($query) use ($validatedAnswers) {
            $query->whereIn('id', collect($validatedAnswers)->pluck('selectedAnswerIds')->flatten())
                  ->where('is_correct', true);
        }]);
    }

}
