<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BarangPage;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckLogin;

// Auth routes (tanpa middleware)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/test-redis', function () {
    Redis::set('testredis', 'laravel-Redis');
    return Redis::get('testredis');
});
// Route utama dilindungi middleware CheckLogin
Route::get('/', BarangPage::class)->name('barang')->middleware(CheckLogin::class);
