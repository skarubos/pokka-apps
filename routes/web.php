<?php

use App\Http\Controllers\MyAppsController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\StartPageController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
Route::view('/rewards/home', 'rewards_home', ['title' => 'Rewards Automation']);

Route::middleware('auth')->group(function () {
    Route::get('/test', [RewardsController::class, 'test']);
});

Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::post('/weather', [WeatherController::class, 'show'])->name('weather.show');

Route::get('/myapps', [MyAppsController::class, 'index'])->name('myapps');
Route::get('/myapps/edit', [MyAppsController::class, 'edit'])->name('myapps.edit');
Route::get('/myapps/sort', [MyAppsController::class, 'sort_show'])->name('myapps.sort');
Route::post('/myapps/sort', [MyAppsController::class, 'sort']);
Route::put('/myapps/{id}', [MyAppsController::class, 'update'])->name('myapps.update');
Route::delete('/myapps/{id}', [MyAppsController::class, 'destroy'])->name('myapps.destroy');

Route::get('/startpage', [StartPageController::class, 'index'])->name('bookmark');
Route::post('/startpage/sort', [StartPageController::class, 'sort'])->name('bookmark.sort');
Route::get('/startpage/form', [StartPageController::class, 'form'])->name('bookmark.form');
Route::post('/startpage/create', [StartPageController::class, 'create'])->name('bookmark.create');
Route::post('/startpage/update', [StartPageController::class, 'update'])->name('bookmark.update');
Route::delete('/startpage/{id}', [StartPageController::class, 'destroy'])->name('bookmark.destroy');