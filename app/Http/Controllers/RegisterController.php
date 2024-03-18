<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterUserRequest $request)
    {


        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agreed_to_terms' => $request->agreed_to_terms,
        ]);

        $token = $user->createToken('appToken')->plainTextToken;

        // For Email verification, I will comment it till I implement that feat
        // $user->sendEmailVerificationNotification();



        return response()->json(['message' => 'User successfully registered.']);
    }
}
