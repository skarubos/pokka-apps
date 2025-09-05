<?php

use App\Http\Controllers\RewardsController;
use App\Http\Controllers\ShoppingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
Route::view('/rewards/home', 'rewards_home', ['title' => 'Rewards Automation']);

Route::get('/shopping', [ShoppingController::class, 'index'])->name('shopping');