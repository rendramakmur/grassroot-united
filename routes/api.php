<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'frontoffice'], function() {
});

Route::group(['prefix' => 'backoffice'], function() {
    Route::post('/login');

    Route::group(['middleware' => 'back-office'], function () {
        Route::get('/user');
    });
});
