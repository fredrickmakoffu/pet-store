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
    
    
    Route::group(['middleware' => 'jwt'], function () {
        // User APIs
        Route::get('users/{user:uuid}', [\App\Http\Controllers\User\UsersController::class, 'show']);
        Route::get('users/edit/{user:uuid}', [\App\Http\Controllers\User\UsersController::class, 'update']);
        Route::get('users/delete/{user:uuid}', [\App\Http\Controllers\User\UsersController::class, 'destroy']);

        // Promotion APIs
        Route::get('promotions', [\App\Http\Controllers\PromotionController::class, 'index']);
        Route::post('promotions', [\App\Http\Controllers\PromotionController::class, 'store']);
        Route::get('promotions/{promotion:uuid}', [\App\Http\Controllers\PromotionController::class, 'show']);
        Route::put('promotions/edit/{promotion:uuid}', [\App\Http\Controllers\PromotionController::class, 'update']);
        Route::delete('promotions/delete/{promotion:uuid}', [\App\Http\Controllers\PromotionController::class, 'destroy']);

        // Brand APIs
        Route::get('brands', [\App\Http\Controllers\BrandController::class, 'index']);
        Route::post('brands', [\App\Http\Controllers\BrandController::class, 'store']);
        Route::get('brands/{brand:uuid}', [\App\Http\Controllers\BrandController::class, 'show']);
        Route::put('brands/edit/{brand:uuid}', [\App\Http\Controllers\BrandController::class, 'update']);
        Route::delete('brands/delete/{brand:uuid}', [\App\Http\Controllers\BrandController::class, 'destroy']);

        // Post APIs
        Route::get('posts', [\App\Http\Controllers\PostsController::class, 'index']);
        Route::post('posts', [\App\Http\Controllers\PostsController::class, 'store']);
        Route::get('posts/{post:uuid}', [\App\Http\Controllers\PostsController::class, 'show']);
        Route::put('posts/edit/{post:uuid}', [\App\Http\Controllers\PostsController::class, 'update']);
        Route::delete('posts/delete/{post:uuid}', [\App\Http\Controllers\PostsController::class, 'destroy']);


    });

    // Admin
    Route::group(['middleware' => 'jwt:admin', 'prefix' => 'admin'], function () {
        Route::post('create', [\App\Http\Controllers\User\AdminController::class, 'store']);
        Route::get('user-listing', [\App\Http\Controllers\User\AdminController::class, 'index']);
        Route::put('user-edit/{user:uuid}', [\App\Http\Controllers\User\AdminController::class, 'update']);
        Route::get('user-delete/{user:uuid}', [\App\Http\Controllers\User\AdminController::class, 'destroy']);
    });

    Route::group(['middleware' => 'jwt'], function () {
        // Admin
        Route::get('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    });
});

