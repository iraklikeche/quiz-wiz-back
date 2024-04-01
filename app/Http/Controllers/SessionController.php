<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function register(RegisterUserRequest $request)
    {

        $user = User::create($request->validated());

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'User successfully registered.']);
    }

    public function login(LoginUserRequest $request)
    {

        $credentials = $request->only('email', 'password');
        if (Auth::attemptWhen($credentials, function (User $user) {
            return $user->hasVerifiedEmail();
        })) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'User successfully logged in.',
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials are incorrect or the email has not been verified.'
        ], 401);


    }

    public function logout(Request $request)
    {
        auth('web')->logout();

        return response()->json(['message' => 'You have been successfully logged out!']);
    }

    public function forgotPassword(Request $request)
    {
        //
    }

    public function resetPassword(Request $request)
    {
        //
    }
}
