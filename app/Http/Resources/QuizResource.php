<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $imageUrl = $this->image ? Storage::disk('public')->url($this->image) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->getImageUrlAttribute(),
            'totalTime' => $this->estimated_time,
            'totalPoints' => $this->total_points,
            'numberOfQuestions' => $this->questions->count(),
            'difficultyLevel' => new DifficultyLevelResource($this->difficultyLevel),
            'categories' => CategoryResource::collection($this->categories),
            'questions' => QuestionResource::collection($this->questions),
        ];
    }
}
