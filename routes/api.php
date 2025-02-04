<?php

use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CompanyDetails;
use App\Http\Controllers\CompanyDetailsController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckLoggedIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'authenticatedUser']);


Route::controller(SessionController::class)->group(function () {
    Route::middleware([CheckLoggedIn::class])->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
        Route::post('/reset-password/resend', 'resendResetLink');
    });

    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::prefix('/email')->controller(VerificationController::class)->group(function () {
    Route::get('/verify/{id}/{hash}', 'verify')
         ->name('verification.verify')
         ->middleware('signed');

    Route::post('/resend', 'resend')
         ->name('verification.resend');
});

Route::controller(QuizController::class)->group(function () {
    Route::get('/quizzes', 'index')->name('quizzes.index');
    Route::get('/quizzes/similar', 'similarQuizzesByCategories')->name('quizzes.similar');
    Route::get('/quizzes/{id}', 'show')->name('quizzes.show');
    Route::get('/categories', 'getAllCategories')->name('categories.index');
    Route::get('/difficulty-levels', 'getAllDifficultyLevels')->name('difficulty.index');
    Route::post('/quizzes/{id}/submit', 'submitAnswers')->name('quizzes.submit');
});



Route::get('/company-details', [CompanyDetailsController::class, 'show']);
