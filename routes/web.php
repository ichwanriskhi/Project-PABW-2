<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\KategoriController;
use App\Http\Controllers\Web\BarangController;
use App\Http\Controllers\Web\LelangController;
use App\Http\Controllers\Web\PenggunaController;
use App\Http\Controllers\Web\PenawaranController;

// Routes untuk Login Admin/Petugas
Route::get('/login/admin', [AuthController::class, 'showLoginAdmin'])->name('login.admin');
Route::post('/login/admin', [AuthController::class, 'loginAdmin'])->name('login.admin.process');

// Routes untuk Register
Route::get('/register/pembeli', [AuthController::class, 'showRegisterPembeli'])->name('register.pembeli');
Route::post('/register/pembeli', [AuthController::class, 'registerPembeli'])->name('register.pembeli.process');

Route::get('/register/penjual', [AuthController::class, 'showRegisterPenjual'])->name('register.penjual');
Route::post('/register/penjual', [AuthController::class, 'registerPenjual'])->name('register.penjual.process');

// Routes untuk Login Penjual
Route::get('/login/penjual', [AuthController::class, 'showLoginPenjual'])->name('login.penjual');
Route::post('/login/penjual', [AuthController::class, 'loginPenjual'])->name('login.penjual.process');

// Routes untuk Login Pembeli
Route::get('/login/pembeli', [AuthController::class, 'showLoginPembeli'])->name('login.pembeli');
Route::post('/login/pembeli', [AuthController::class, 'loginPembeli'])->name('login.pembeli.process');

// Route Logout (bisa digunakan dari semua role)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk marketplace (bisa diakses tanpa login)
// Route::get('/marketplace', [BarangController::class, 'marketplace'])->name('marketplace');

// Routes yang memerlukan autentikasi
Route::middleware(['auth:web'])->group(function () {

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AuthController::class, 'adminIndex'])->name('admin.index');

        // Kategori routes untuk Admin
        Route::get('/admin/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/admin/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('/admin/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/admin/kategori/{id}', [KategoriController::class, 'show'])->name('kategori.show');
        Route::get('/admin/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/admin/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/admin/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        // Barang routes untuk Admin
        Route::get('/admin/barang', [BarangController::class, 'index'])->name('admin.barang.index');
        Route::get('/admin/barang/create', [BarangController::class, 'create'])->name('admin.barang.create');
        Route::post('/admin/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/admin/barang/{id}', [BarangController::class, 'show'])->name('admin.barang.show');
        Route::get('/admin/barang/{id}/edit', [BarangController::class, 'edit'])->name('admin.barang.edit');
        Route::put('/admin/barang/{id}', [BarangController::class, 'update'])->name('admin.barang.update');
        Route::delete('/admin/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

        // Routes khusus admin untuk manajemen status barang
        Route::get('/admin/barang/status/{status}', [BarangController::class, 'getByStatus'])->name('barang.status');
        Route::get('/admin/barang/{id}/edit-status', [BarangController::class, 'editStatus'])->name('barang.edit.status');
        Route::patch('/admin/barang/{id}/status', [BarangController::class, 'updateStatus'])->name('admin.update.status');
        Route::get('/admin/barang/kategori/{id_kategori}', [BarangController::class, 'getByKategori'])->name('barang.kategori');

        // Tambahkan route admin lainnya di sini
        // tampilkan lelang index
        Route::get('/admin/lelang', [LelangController::class, 'index'])->name('admin.lelang.index');
        Route::get('/admin/lelang/{id}', [LelangController::class, 'show'])->name('admin.lelang.show');
        Route::patch('/admin/lelang/{id}/tutup', [LelangController::class, 'close'])->name('admin.lelang.tutup');
        Route::patch('/admin/lelang/{id}/selesai', [LelangController::class, 'complete'])->name('admin.lelang.selesai');

        Route::get('/admin/petugas', [PenggunaController::class, 'index'])->name('admin.petugas.index');
        Route::post('/admin/petugas', [PenggunaController::class, 'store'])->name('admin.petugas.store');
        Route::get('/admin/petugas/create', [PenggunaController::class, 'create'])->name('admin.petugas.create');
        Route::get('/admin/petugas/{id}/edit', [PenggunaController::class, 'edit'])->name('admin.petugas.edit');
        Route::put('/admin/petugas/{id}', [PenggunaController::class, 'update'])->name('admin.petugas.update');
        Route::delete('/admin/petugas/{id}', [PenggunaController::class, 'destroy'])->name('admin.petugas.destroy');

        Route::get('/pengguna', [PenggunaController::class, 'dataPengguna'])->name('admin.pengguna.index');
        Route::get('/pengguna/search', [PenggunaController::class, 'searchPengguna'])->name('admin.pengguna.search');
    });

    // Petugas routes
    Route::middleware(['role:petugas'])->group(function () {
        Route::get('/petugas', [AuthController::class, 'petugasIndex'])->name('petugas.index');

        // Kategori routes untuk Petugas
        Route::get('/petugas/kategori', [KategoriController::class, 'index'])->name('petugas.kategori.index');
        Route::get('/petugas/kategori/create', [KategoriController::class, 'create'])->name('petugas.kategori.create');
        Route::post('/petugas/kategori', [KategoriController::class, 'store'])->name('petugas.kategori.store');
        Route::get('/petugas/kategori/{id}', [KategoriController::class, 'show'])->name('petugas.kategori.show');
        Route::get('/petugas/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('petugas.kategori.edit');
        Route::put('/petugas/kategori/{id}', [KategoriController::class, 'update'])->name('petugas.kategori.update');
        Route::delete('/petugas/kategori/{id}', [KategoriController::class, 'destroy'])->name('petugas.kategori.destroy');

        // Barang routes untuk Petugas
        Route::get('/petugas/barang', [BarangController::class, 'index'])->name('petugas.barang.index');
        Route::get('/petugas/barang/create', [BarangController::class, 'create'])->name('petugas.barang.create');
        Route::post('/petugas/barang', [BarangController::class, 'store'])->name('petugas.barang.store');
        Route::get('/petugas/barang/{id}', [BarangController::class, 'show'])->name('petugas.barang.show');
        Route::get('/petugas/barang/{id}/edit', [BarangController::class, 'edit'])->name('petugas.barang.edit');
        Route::put('/petugas/barang/{id}', [BarangController::class, 'update'])->name('petugas.barang.update');
        Route::delete('/petugas/barang/{id}', [BarangController::class, 'destroy'])->name('petugas.barang.destroy');

        // Routes khusus petugas untuk manajemen status barang
        Route::get('/petugas/barang/status/{status}', [BarangController::class, 'getByStatus'])->name('petugas.barang.status');
        Route::get('/petugas/barang/{id}/edit-status', [BarangController::class, 'editStatus'])->name('petugas.barang.edit.status');
        Route::patch('/petugas/barang/{id}/status', [BarangController::class, 'updateStatus'])->name('petugas.update.status');
        Route::get('/petugas/barang/kategori/{id_kategori}', [BarangController::class, 'getByKategori'])->name('petugas.barang.kategori');

        // Tambahkan route petugas lainnya di sini
        Route::get('/petugas/lelang', [LelangController::class, 'index'])->name('petugas.lelang.index');
        Route::get('/petugas/lelang/{id}', [LelangController::class, 'show'])->name('petugas.lelang.show');
        Route::patch('/petugas/lelang/{id}/tutup', [LelangController::class, 'close'])->name('petugas.lelang.tutup');
        Route::patch('/petugas/lelang/{id}/selesai', [LelangController::class, 'complete'])->name('petugas.lelang.selesai');
    });

    // Penjual routes
    Route::middleware(['role:penjual'])->group(function () {
        Route::get('/penjual', [AuthController::class, 'penjualIndex'])->name('penjual.index');

        // Barang routes untuk Penjual (hanya bisa mengelola barang miliknya)
        Route::get('/penjual/barang', [BarangController::class, 'index'])->name('penjual.barang.index');
        Route::get('/penjual/barang/create', [BarangController::class, 'create'])->name('penjual.barang.create');
        Route::post('/penjual/barang', [BarangController::class, 'store'])->name('penjual.barang.store');
        Route::get('/penjual/barang/{id}', [BarangController::class, 'show'])->name('penjual.barang.show');
        Route::get('/penjual/barang/{id}/edit', [BarangController::class, 'edit'])->name('penjual.barang.edit');
        Route::put('/penjual/barang/{id}', [BarangController::class, 'update'])->name('penjual.barang.update');

        // Tambahkan route penjual lainnya di sini
        Route::get('/penjual/lelang/{id}', [LelangController::class, 'show'])->name('penjual.lelang.show');
        Route::get('penjual/bantuan', [AuthController::class, 'bantuan'])->name('penjual.bantuan');
    });

    // Pembeli routes
    Route::middleware(['role:pembeli'])->group(function () {
        Route::get('/pembeli', [AuthController::class, 'pembeliIndex'])->name('pembeli.index');

        // Barang routes untuk Pembeli (hanya melihat barang yang disetujui)
        Route::get('/pembeli/barang', [BarangController::class, 'index'])->name('pembeli.barang.index');
        Route::get('/pembeli/barang/{id}', [BarangController::class, 'showDetailForPembeli'])->name('pembeli.barang.detail');
        Route::post('/pembeli/penawaran', [PenawaranController::class, 'store'])->name('penawaran.store');
        Route::get('/pembeli/penawaran/{id}', [PenawaranController::class, 'show'])->name('penawaran.show');
        Route::get('/pembeli/aktivitas', [PenawaranController::class, 'history'])->name('pembeli.aktivitas');
        Route::put('/pembeli/penawaran/{id}/payment', [PenawaranController::class, 'updatePayment'])->name('penawaran.updatePayment');
        Route::get('/aktivitas', [PenawaranController::class, 'history'])->name('aktivitas');
        Route::get('/bantuan', [AuthController::class, 'bantuan'])->name('pembeli.bantuan');

        // Tambahkan route pembeli lainnya di sini
    });
});

// Redirect default ke index
Route::get('/', function () {
    return view('index');
})->name('landing');
