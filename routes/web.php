<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BarangPage;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckLogin;




Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', BarangPage::class)->name('barang')->middleware(CheckLogin::class);
