<?php

use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\CheckLoggedIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(SessionController::class)->group(function () {
    Route::middleware([CheckLoggedIn::class])->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
        Route::post('/reset-password/resend', 'resendResetLink');
    });

    Route::post('/logout', 'logout')->middleware('auth');

});

Route::prefix('/email')->controller(VerificationController::class)->group(function () {
    Route::get('/verify/{id}/{hash}', 'verify')
         ->name('verification.verify')
         ->middleware('signed');

    Route::post('/resend', 'resend')
         ->name('verification.resend');
});


Route::controller(QuizController::class)->group(function () {
    Route::get('/quizzes/search', 'search');
    Route::get('/quizzes', 'index');
    Route::get('quizzes/{id}', 'show');
});
