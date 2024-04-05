<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function register(RegisterUserRequest $request): JsonResponse
    {


        $user = User::create($request->validated());

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'User successfully registered.']);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {


        $credentials = $request->only('email', 'password');
        $remember = $request->input('remember', false);

        if (Auth::attemptWhen($credentials, function (User $user) {
            return $user->hasVerifiedEmail();
        }, $remember)) {
            $request->session()->regenerate();

            return response()->json([
                'message' => 'User successfully logged in.',
            ]);
        }

        return response()->json([
            'message' => 'The provided credentials are incorrect or the email has not been verified.'
        ], 401);


    }

    public function logout(Request $request): JsonResponse
    {
        auth('web')->logout();

        return response()->json(['message' => 'You have been successfully logged out!']);
    }

    public function forgotPassword(Request $request): JsonResponse
    {

        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => __($status)]);
        }

        return response()->json(['email' => __($status)], 400);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:3',
        ]);



        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new Password($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['status' => __($status)]);
        } elseif ($status === Password::INVALID_TOKEN) {
            return response()->json(['error' => 'The password reset link is expired or invalid.'], 422);
        }

        return response()->json(['email' => [__($status)]], 400);
    }


    public function resendResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'success', 'message' => __($status)]);
        }

        return response()->json(['status' => 'error', 'message' => __($status)], 400);
    }
}
