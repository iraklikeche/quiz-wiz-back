<?php

namespace App\Http\Middleware;

use Laravel\Sanctum\Http\Middleware\AuthenticateSession;

class AuthenticateSanctumRequest extends AuthenticateSession
{
    protected function storePasswordHashInSession($request, string $guard)
    {
        if (! auth($guard)->user()) {
            return;
        }

        $request->session()->put([
            "password_hash_{$guard}" => $request->user()->getAuthPassword(),
        ]);
    }
}
