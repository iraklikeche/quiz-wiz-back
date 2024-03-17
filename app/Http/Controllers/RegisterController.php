<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users|min:3',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:3|confirmed',
            'agreed_to_terms' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'agreed_to_terms' => $request->agreed_to_terms,
        ]);

        $token = $user->createToken('appToken')->plainTextToken;

        // $user->sendEmailVerificationNotification();



        return response()->json(['message' => 'User successfully registered.']);
    }
}
