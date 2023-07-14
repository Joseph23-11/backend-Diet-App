<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LunchController;
use App\Http\Controllers\SnackController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\DinnerController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\PrediksiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BreakfastController;
use App\Http\Controllers\DailyDietController;
use App\Http\Controllers\DailySportController;
use App\Http\Controllers\PersonalDetailController;
use App\Http\Controllers\PerubahanBeratController;

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
    Route::get('/breakfasts', [BreakfastController::class, 'index']);
    Route::post('/breakfasts', [BreakfastController::class, 'store']);
    Route::put('/breakfasts/{id}', [BreakfastController::class, 'update']);
    Route::delete('/breakfasts/{id}', [BreakfastController::class, 'destroy']);
    Route::get('/lunches', [LunchController::class, 'index']);
    Route::post('/lunches', [LunchController::class, 'store']);
    Route::put('/lunches/{id}', [LunchController::class, 'update']);
    Route::delete('/lunches/{id}', [LunchController::class, 'destroy']);
    Route::get('/dinners', [DinnerController::class, 'index']);
    Route::post('/dinners', [DinnerController::class, 'store']);
    Route::put('/dinners/{id}', [DinnerController::class, 'update']);
    Route::delete('/dinners/{id}', [DinnerController::class, 'destroy']);
    Route::get('/snacks', [SnackController::class, 'index']);
    Route::post('/snacks', [SnackController::class, 'store']);
    Route::put('/snacks/{id}', [SnackController::class, 'update']);
    Route::delete('/snacks/{id}', [SnackController::class, 'destroy']);
    Route::get('/daily-sports', [DailySportController::class, 'index']);
    Route::post('/daily-sports', [DailySportController::class, 'store']);
    Route::delete('/daily-sports/{id}', [DailySportController::class, 'destroy']);
    Route::get('/daily-diets', [DailyDietController::class, 'index']);
    Route::get('/status', [DailyDietController::class, 'status']);
    Route::post('/daily-diets/search', [DailyDietController::class, 'searchByDate']);
    Route::get('/perubahan-berat', [PerubahanBeratController::class, 'index']);
    Route::post('/perubahan-berat', [PerubahanBeratController::class, 'store']);
    Route::delete('/perubahan-berat/{id}', [PerubahanBeratController::class, 'destroy']);
    Route::get('/prediksi', [PrediksiController::class, 'index']);
});
