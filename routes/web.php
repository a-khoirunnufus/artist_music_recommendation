<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\RecommenderController::class, 'index']);
Route::get('/pre-recommendation', [\App\Http\Controllers\RecommenderController::class, 'preRecommendation']);
Route::post('/engagement', [\App\Http\Controllers\RecommenderController::class, 'engagement']);
Route::get('/recommendation', [\App\Http\Controllers\RecommenderController::class, 'getRecommendation']);
Route::get('/reset-preferences', [\App\Http\Controllers\RecommenderController::class, 'resetPreferences']);

Route::get('/choose', function() {
    return view('choose');
});
Route::get('/result', function() {
    return view('result');
});
