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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('users', function() {
    return \App\Models\User::all();
})->middleware('jwt');

Route::get('test/tokens', function() {  
    return JwtToken::where('token', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJhNDkyZmM4OC1kNjkyLTQzMGQtOWQ4Ny1lODRhYTExZmQ5NzgiLCJpYXQiOjE2ODEwODY2ODIuMDEzMzI3LCJuYmYiOjE2ODEwODY3NDIuMDEzMzI3LCJleHAiOjE2ODEwOTAyODIuMDEzMzI3LCJ0aW1lem9uZSI6IkFmcmljYS9OYWlyb2JpIiwidWlkIjoxfQ.w7Jf80TpDF4FIJY86w784xOOncCsMdxKjutmV6Zpu58')->get();
});

