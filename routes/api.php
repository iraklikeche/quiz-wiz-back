<?php

use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);

// Route::get('/email/verify', function (Request $request) {
//     $frontendUrl = env('FRONTEND_URL', 'http://localhost');
//     return $request->user()->hasVerifiedEmail()
//                 ? redirect($frontendUrl . '/verified')
//                 : view('auth.verify-email');
// })->middleware('auth:sanctum')->name('verification.notice');


// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $frontendUrl = env('FRONTEND_URL', 'http://localhost');
//     $request->fulfill();
//     return redirect($frontendUrl . '/verified');
// })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Route::post('/email/verification-notification', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();
//     return ['message' => 'Verification link sent!'];
// })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
