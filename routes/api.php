<?php

use App\Models\JwtToken;
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

Route::group(['prefix' => 'v1'], function () {
    // Auth Routes
    Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
    
    Route::group(['prefix' => 'admin'], function () {
        // User
        Route::group(['middleware' => 'jwt:admin'], function ()   {
            Route::get('users', function() {
                return \App\Models\User::all();
            });
        });
    });
});

