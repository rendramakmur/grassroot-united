<?php

use App\Http\Controllers\BackOffice\Credential\BackOfficeCredentialController;
use App\Http\Controllers\BackOffice\GameData\BackOfficeGameDataController;
use App\Http\Controllers\BackOffice\UserInformation\BackOfficeUserInformationController;
use App\Http\Controllers\FrontOffice\Credential\FrontOfficeCredentialController;
use App\Http\Controllers\FrontOffice\UserInformation\FrontOfficeUserInformationController;
use Illuminate\Routing\RouteGroup;
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

Route::post('/login', [FrontOfficeCredentialController::class, 'login']);
Route::post('/register', [FrontOfficeCredentialController::class, 'register']);
Route::get('/activate/{userNumber}/{activationCode}', [FrontOfficeCredentialController::class, 'activate']);

Route::group(['middleware' => 'frontoffice'], function() { 
    Route::get('/user/{userNumber}', [FrontOfficeUserInformationController::class, 'detail']);
    Route::put('/user/{userNumber}', [FrontOfficeUserInformationController::class, 'update']);
});

Route::group(['prefix' => 'backoffice'], function() {
    Route::post('/login', [BackOfficeCredentialController::class, 'login']);

    Route::group(['middleware' => 'backoffice'], function () {
        Route::get('/user/{userNumber}', [BackOfficeUserInformationController::class, 'detail']);
        Route::get('/user', [BackOfficeUserInformationController::class, 'index']);
        Route::post('/user', [BackOfficeUserInformationController::class, 'create']);
        Route::put('/user/{userNumber}', [BackOfficeUserInformationController::class, 'update']);
        Route::delete('/user/{userNumber}', [BackOfficeUserInformationController::class, 'delete']);

        Route::put('/game/info/{gameNumber}', [BackOfficeGameDataController::class, 'updateGameInfo']);
        Route::put('/game/gallery/{gameNumber}', [BackOfficeGameDataController::class, 'updateGameGallery']);
        Route::post('/game/player/register/{gameNumber}', [BackOfficeGameDataController::class, 'playerRegister']);
        Route::post('/game/player/paid/{gameNumber}', [BackOfficeGameDataController::class, 'playerPaid']);
        Route::delete('/game/player/paid/{gameNumber}', [BackOfficeGameDataController::class, 'deletePaidPlayer']);
        Route::get('/game/{userNumber}', [BackOfficeGameDataController::class, 'detail']);
        Route::get('/game', [BackOfficeGameDataController::class, 'index']);
        Route::post('/game', [BackOfficeGameDataController::class, 'create']);
        Route::put('/game/{gameNumber}', [BackOfficeGameDataController::class, 'update']);
        Route::delete('/game/{gameNumber}', [BackOfficeGameDataController::class, 'delete']);
    });
});
