<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\PenawaranController;

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
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
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

    // Penawaran routes
    Route::post('/penawaran', [PenawaranController::class, 'store']);
    Route::get('/penawaran/aktivitas', [PenawaranController::class, 'history']);
    Route::get('/penawaran/{id}', [PenawaranController::class, 'show']);
    
    // Barang routes
    Route::get('/barang/approved', [BarangController::class, 'getApprovedBarang']);
    Route::get('/barang/approved-pembeli', [BarangController::class, 'getApprovedBarangForPembeli']);
    Route::get('/barang/my-barang', [BarangController::class, 'getMyBarang']);
    Route::get('/barang/kategori-filter', [BarangController::class, 'getKategoriForFilter']);
    Route::get('/barang/status/{status}', [BarangController::class, 'getByStatus']);
    Route::get('/barang/kategori/{id_kategori}', [BarangController::class, 'getByKategori']);
    Route::get('/barang/detail-pembeli/{id}', [BarangController::class, 'showDetailForPembeli']);
    
    // Status update route
    Route::put('/barang/{id}/status', [BarangController::class, 'updateStatus']);
    
    // Standard CRUD routes (put these LAST to avoid conflicts)
    Route::get('/barang', [BarangController::class, 'index']);
    Route::post('/barang', [BarangController::class, 'store']);
    Route::get('/barang/{id}', [BarangController::class, 'show']);
    Route::put('/barang/{id}', [BarangController::class, 'update']);
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);
});