<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'timeSpent' => 'required|integer|min:0',
            'answers' => 'required|array',
            'answers.*.questionId' => 'required|exists:questions,id',
            'answers.*.selectedAnswerIds' => 'required|array',
            'answers.*.selectedAnswerIds.*' => 'exists:answers,id'
        ];
    }
}
