<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BarangPage;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckLogin;

Route::view('/logic/no2-no3', 'logic.no2-no3');
Route::view('/logic/no4', 'logic.no4');
Route::view('/logic/no5', 'logic.no5');
Route::view('/logic/no6', 'logic.no6');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', BarangPage::class)->name('barang')->middleware(CheckLogin::class);
