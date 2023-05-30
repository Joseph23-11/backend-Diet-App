<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PersonalDetailController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/personal-details', [PersonalDetailController::class, 'store']);
    Route::get('/personal-details', [PersonalDetailController::class, 'show']);
    Route::put('/personal-details', [PersonalDetailController::class, 'update']);
    Route::get('/target', [TargetController::class, 'show']);
    Route::post('/target', [TargetController::class, 'store']);
    Route::get('/food', [FoodController::class, 'index']);
    Route::get('/sports', [SportController::class, 'index']);
});
