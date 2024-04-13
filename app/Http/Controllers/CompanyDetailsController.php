<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyDetailsController extends Controller
{
    public function show()
    {
        $details = [
            'email' => 'quizwiz@gmail.com',
            'number' => '+995 328989',
            'facebook_link' => 'https://facebook.com/user',
            'linkedIn' => 'https://linkedin.com/in/user'
        ];

        return response()->json($details);
    }

}
