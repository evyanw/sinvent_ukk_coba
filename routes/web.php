<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
 return view('login');
});

Route::resource('/kategori', \App\Http\Controllers\KategoriController::class)->middleware('auth'); 

Route::resource('/dashboard', \App\Http\Controllers\DashboardController::class)->middleware('auth'); 

Route::resource('/barang', \App\Http\Controllers\BarangController::class)->middleware('auth'); 

Route::resource('/barangmasuk', \App\Http\Controllers\BarangMasukController::class)->middleware('auth'); 

Route::resource('/barangkeluar', \App\Http\Controllers\BarangKeluarController::class)->middleware('auth'); 


// route untuk login, logout, dan register 

//route login 
Route::get('login', [LoginController::class,'index'])->name('login')->middleware('guest'); 
Route::post('login', [LoginController::class,'authenticate']); 

//route logout 
Route::get('logout', [LoginController::class,'logout']); 
Route::post('logout', [LoginController::class,'logout']); 

//route register 
Route::get('register', [RegisterController::class,'create']); 
Route::post('register', [RegisterController::class,'store']);
