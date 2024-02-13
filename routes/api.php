<?php

use App\Http\Controllers\BackOffice\Credential\BackOfficeCredentialController;
use App\Http\Controllers\BackOffice\UserInformation\BackOfficeUserInformationController;
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
    Route::post('/login', [BackOfficeCredentialController::class, 'login']);
    Route::get('/user/activate/{userNumber}/{activationCode}', [BackOfficeUserInformationController::class, 'activate']);

    Route::group(['middleware' => 'backoffice'], function () {
        Route::get('/user/{userNumber}', [BackOfficeUserInformationController::class, 'detail']);
        Route::get('/user', [BackOfficeUserInformationController::class, 'index']);
        Route::post('/user', [BackOfficeUserInformationController::class, 'create']);
        Route::put('/user/{userNumber}', [BackOfficeUserInformationController::class, 'update']);
        Route::delete('/user/{userNumber}', [BackOfficeUserInformationController::class, 'delete']);
    });
});
