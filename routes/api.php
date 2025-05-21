<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BarangController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute publik
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/register-seller', [App\Http\Controllers\Api\AuthController::class, 'registerSeller']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

// Rute terproteksi dengan guard 'api'
Route::middleware('auth:api')->group(function() {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // Pengguna routes
    Route::get('/pengguna', [PenggunaController::class, 'index']);
    Route::post('/pengguna', [PenggunaController::class, 'store']);
    Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);
    
    // Kategori routes
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::post('/kategori', [KategoriController::class, 'store']);
    Route::get('/kategori/{id}', [KategoriController::class, 'show']);
    Route::put('/kategori/{id}', [KategoriController::class, 'update']);
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);
    
    // Barang routes
    Route::get('/barang', [BarangController::class, 'index']);
    Route::post('/barang', [BarangController::class, 'store']);
    Route::get('/barang/{id}', [BarangController::class, 'show']);
    Route::put('/barang/{id}', [BarangController::class, 'update']);
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);
    Route::get('/barang/approved', [BarangController::class, 'getApprovedBarang']);
    Route::get('/barang/status/{status}', [BarangController::class, 'getByStatus']);
    Route::get('/barang/kategori/{id_kategori}', [BarangController::class, 'getByKategori']);
    Route::get('/my-barang', [BarangController::class, 'getMyBarang']);
    Route::put('/barang/{id}/status', [BarangController::class, 'updateStatus']);
});