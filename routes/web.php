<?php
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\QrMasukController;
use App\Http\Controllers\IzinMasukController;
use App\Http\Controllers\LobiController;
use Illuminate\Support\Facades\Route;

// Halaman publik siswa — tanpa login.
Route::get('/', [IzinMasukController::class, 'create'])->name('izin.create');
Route::post('/izin', [IzinMasukController::class, 'store'])->name('izin.store');
Route::get('/izin/{kode}', [IzinMasukController::class, 'show'])->name('izin.show');
Route::get('/izin/{kode}/status', [IzinMasukController::class, 'status'])->name('izin.status');
Route::post('/izin/{kode}/scan', [IzinMasukController::class, 'scan'])->name('izin.scan');

// QR statis lobi — tujuan scan QR cetak.
Route::get('/lobi/{kode}', [LobiController::class, 'show'])->name('lobi.show');
Route::post('/lobi/{kode}/konfirmasi', [LobiController::class, 'konfirmasi'])->name('lobi.konfirmasi');

// Admin.
Route::get('/admin/login', [AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.store');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'rekap'])->name('admin.rekap');
    Route::get('/rekap', [AdminController::class, 'rekap'])->name('admin.rekap.alt');
    Route::get('/rekap/export', [AdminController::class, 'exportCsv'])->name('admin.rekap.export');
    Route::post('/izin/{izin}/diizinkan', [AdminController::class, 'tandaiDiizinkan'])->name('admin.izin.diizinkan');
    Route::get('/qr', [QrMasukController::class, 'index'])->name('admin.qr.index');
    Route::post('/qr', [QrMasukController::class, 'store'])->name('admin.qr.store');
    Route::get('/qr/{qr}/cetak', [QrMasukController::class, 'cetak'])->name('admin.qr.cetak');
    Route::post('/qr/{qr}/toggle', [QrMasukController::class, 'toggle'])->name('admin.qr.toggle');
    Route::post('/qr/{qr}/regenerate', [QrMasukController::class, 'regenerate'])->name('admin.qr.regenerate');
    Route::delete('/qr/{qr}', [QrMasukController::class, 'destroy'])->name('admin.qr.destroy');
});

Route::get('/offline', fn () => view('offline'))->name('offline');
