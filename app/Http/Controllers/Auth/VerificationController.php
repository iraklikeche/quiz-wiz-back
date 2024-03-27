<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
            return response()->json(['message' => 'The verification link has expired.'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            // return response()->json(['message' => 'Email is already verified.']);
            return redirect(config('app.frontend_url') . '/login?verified=already');

        } else {
            $user->markEmailAsVerified();
            // return response()->json(['message' => 'Email verified successfully.']);
            return redirect(config('app.frontend_url') . '/login?verified=true');

        }
    }

}
