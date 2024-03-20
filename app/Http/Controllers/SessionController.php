<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agreed_to_terms' => $request->agreed_to_terms,
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'User successfully registered.']);
    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $user->createToken('yourAppNameToken')->plainTextToken;

        return response()->json([
            'message' => 'User successfully logged in.',
            'token' => $token,
        ]);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
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
