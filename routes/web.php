<?php

use App\Http\Controllers\RewardsController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
Route::view('/rewards/home', 'rewards_home', ['title' => 'Rewards Automation']);

Route::get('/test', [RewardsController::class, 'test']);

Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::post('/weather', [WeatherController::class, 'show'])->name('weather.show');