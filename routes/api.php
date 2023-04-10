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

    // Validate User
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\User\UsersController::class, 'verify'])->name('verification.verify');
    
    Route::group(['middleware' => 'jwt:admin', 'prefix' => 'admin'], function () {
        // Admin
        Route::post('create', [\App\Http\Controllers\User\UsersController::class, 'store']);
        Route::get('user-listing', [\App\Http\Controllers\User\UsersController::class, 'index']);
        Route::put('user-edit/{id}', [\App\Http\Controllers\User\UsersController::class, 'update']);
        Route::get('user-delete/{id}', [\App\Http\Controllers\User\UsersController::class, 'destroy']);
    });

    Route::group(['middleware' => 'jwt'], function () {
        // Admin
        Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    });
});

use App\Models\User;
use Illuminate\Auth\Events\Registered;

Route::get('email/{id}', function(User $user) {
    try {
        event(new Registered($user));
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }

    return 'here';
});

