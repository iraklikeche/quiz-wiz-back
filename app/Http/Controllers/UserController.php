<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function authenticatedUser(Request $request)
    {
        return new UserResource($request->user());
    }
}
