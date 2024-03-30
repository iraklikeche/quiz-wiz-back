<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResendVerificationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link or user not found.'], 404);
        }

        if (!URL::hasValidSignature($request)) {
            return response()->json([ 'message' => 'Verification link is expired.']);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([ 'message' => 'User is already verified.'], 422);

        } else {
            $user->markEmailAsVerified();
            return response()->json(['message' => 'User has been verified.'], );
        }
    }


    public function resend(ResendVerificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No user could be found with this email address.'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent.']);

    }

}
