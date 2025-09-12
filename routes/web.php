<?php

use App\Http\Controllers\RewardsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
Route::view('/rewards/home', 'rewards_home', ['title' => 'Rewards Automation']);

Route::get('/test', [RewardsController::class, 'test']);