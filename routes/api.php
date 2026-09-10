<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AppConfigController;
use App\Http\Controllers\Api\AppVersionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\External\ActivityController as ExternalActivityController;
use App\Http\Controllers\Api\External\DestinationController as ExternalDestinationController;
use App\Http\Controllers\Api\External\NationalityController as ExternalNationalityController;
use App\Http\Controllers\Api\LoginOtpController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//API route for login user
Route::post('/login', [AuthController::class, 'login']);
Route::get('/version', [AppVersionController::class, 'show']);
Route::get('/variables/{code}', [AppConfigController::class, 'show']);

//API route for register user
Route::post('/register', [RegisterController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/request-otp', [LoginOtpController::class, 'requestOtp']);
    Route::post('/login-otp', [LoginOtpController::class, 'authenticate']);
    Route::post('/register-onesignal', [ProfileController::class, 'subscribeOnesignal'])
        ->name("register-onesignal");

    // API route for logout user
    // Route::group(['middleware' => ['auth.verified']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/creds', [ProfileController::class, 'updateCredentials']);

    Route::get("/activity", [ActivityController::class, "index"]);
    Route::get("/activity/{manifest}", [ActivityController::class, "show"]);
    Route::post("/activity/{manifest}/approval", [ActivityController::class, "approval"]);
    Route::post("/activity/scan", [ActivityController::class, "scan"]);
    // });
});

// External partner API (e.g. Semporna Jetty Resort - Manifest Form), authenticated via static bearer token.
Route::prefix('external')
    ->middleware(['external.token'])
    ->group(function () {
        Route::get('/destinations', [ExternalDestinationController::class, 'index']);
        Route::get('/activities', [ExternalActivityController::class, 'index']);
        Route::get('/nationalities', [ExternalNationalityController::class, 'index']);
    });
