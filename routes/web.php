<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/password/reset/{token}', function () {
    // Frontend app handles the password reset form
})->name('password.reset');
