<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|unique:users|min:3',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:3|confirmed',
            'password_confirmation' => 'same:password|required',
            'agreed_to_terms' => 'accepted',
        ];
    }
}
