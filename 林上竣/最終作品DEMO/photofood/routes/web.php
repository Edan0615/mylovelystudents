<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// 食物分析路由
Route::middleware('auth')->group(function () {
    Route::get('/food-analyses', [App\Http\Controllers\FoodAnalysisController::class, 'index'])->name('food-analyses.index');
    Route::get('/chart', function () {
        return view('chart');
    })->name('chart');
    Route::get('/api/food-analyses', [App\Http\Controllers\FoodAnalysisController::class, 'index'])->name('food-analyses.api');
    Route::delete('/food-analyses/{id}', [App\Http\Controllers\FoodAnalysisController::class, 'destroy'])->name('food-analyses.destroy');
    Route::get('/api/calories/daily', [App\Http\Controllers\FoodAnalysisController::class, 'getDailyCalories'])->name('calories.daily');
    Route::post('/api/food/analyze', [App\Http\Controllers\FoodAnalysisController::class, 'analyzeFood'])->name('food.analyze');
    Route::post('/api/food/analyze-url', [App\Http\Controllers\FoodAnalysisController::class, 'analyzeFoodByUrl'])->name('food.analyze-url');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
