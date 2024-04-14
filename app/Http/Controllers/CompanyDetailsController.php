<?php

namespace App\Http\Controllers;

use App\Models\CompanyDetail;
use Illuminate\Http\Request;

class CompanyDetailsController extends Controller
{
    public function show()
    {
        $companyDetail = CompanyDetail::firstOrFail();

        return response()->json([
            'email' => $companyDetail->email,
            'number' => $companyDetail->number,
            'facebook_link' => $companyDetail->facebook_link,
            'linkedIn' => $companyDetail->linkedin,
        ]);
    }

}
