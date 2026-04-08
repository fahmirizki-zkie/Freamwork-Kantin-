<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangSementaraController;
use App\Http\Controllers\PilihKotaController;
use App\Http\Controllers\BarangSementaraDtController;
use App\Http\Controllers\WeekEmpat;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorMenuController;
use App\Http\Controllers\PaymentController;

// ==========================================
// 1. DOMAIN VENDOR (http://vendor.localhost:8000)
// ==========================================
Route::domain('vendor.localhost')->group(function () {

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('/verify-otp', [GoogleAuthController::class, 'showOtpForm']);
Route::post('/verify-otp', [GoogleAuthController::class, 'verifyOtp']);


// Protected Routes (Require Authentication)
Route::middleware(['auth'])->group(function () {


    // Route untuk Kategori
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    // Route untuk Buku
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::post('/buku', [BukuController::class, 'store'])->name('buku.store');
    Route::get('/buku/{id}/edit', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('/buku/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');

    // Route untuk Barang
    Route::get('/barang/index', [BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::post('/label/form', [BarangController::class, 'formLabel'])->name('label.form');
    Route::post('/label/generate', [BarangController::class, 'generateLabel'])->name('label.generate');
    Route::get('/barang-sementara', [BarangSementaraController::class, 'index'])->name('barang.sementara');
    Route::get('/barang-sementara-dt', [BarangSementaraDtController::class, 'index'])->name('barang.sementara.dt');
    // Route untuk PDF
    Route::get('/pdf/sertifikat', [PdfController::class, 'previewSertifikat']);
    Route::get('/pdf/sertifikat/download', [PdfController::class, 'downloadSertifikat']);

    Route::get('/pdf/undangan', [PdfController::class, 'previewUndangan']);
    Route::get('/pdf/undangan/download', [PdfController::class, 'downloadUndangan']);

    // Route untuk Pilih Kota
    Route::get('/pilih-kota', [PilihKotaController::class, 'index'])->name('pilih.kota');

    // Studi kasus API wilayah administrasi Indonesia
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    // Endpoint AJAX — GET karena hanya ambil data
    Route::get('/wilayah/kota/{province_id}', [WilayahController::class, 'getKota']) ->name('wilayah.kota');
    Route::get('/wilayah/kecamatan/{regency_id}', [WilayahController::class, 'getKecamatan'])->name('wilayah.kecamatan');
    Route::get('/wilayah/kelurahan/{district_id}', [WilayahController::class, 'getKelurahan'])->name('wilayah.kelurahan');


    // Halaman POS
    Route::get('/pos', [PosController::class, 'index']) ->name('pos.index');
    // AJAX: cari barang by kode (dipanggil saat Enter di input kode)
    Route::get('/pos/cari-barang/{kode}', [PosController::class, 'cariBarang'])->name('pos.cari_barang');
    // AJAX: simpan transaksi (dipanggil saat klik Bayar)
    Route::post('/pos/bayar', [PosController::class, 'bayar'])->name('pos.bayar');
    
    
    
    //Latihan AJAX
    // Route untuk tampilkan halaman
    Route::get('/week4', [WeekEmpat::class, 'index']) ->name('week4.index');

    // Route untuk terima data dari AJAX
    Route::post('/week4/ajax_submit', [WeekEmpat::class, 'submit'])->name('week4.ajax_submit');

  
    // 1. Dashboard Vendor
    Route::get('/', [VendorDashboardController::class, 'index'])->name('vendor.dashboard');
    // 2. Data AJAX untuk Chart di Dashboard
    Route::get('/chart-data', [VendorDashboardController::class, 'getChartData'])->name('vendor.chart');
    // 3. Modul Master Menu (Mencakup tampil list, simpan, update, destroy)
    Route::resource('/menu', VendorMenuController::class)->names('vendor.menu');
});
});




// ==========================================
// 2. DOMAIN CUSTOMER (http://localhost:8000)
// ==========================================
Route::domain('localhost')->group(function () {
    
     // Halaman utama customer (pemesanan)
    Route::get('/', [CustomerController::class, 'index']);

     // AJAX: ambil menu berdasarkan vendor
    Route::get('/menu/{vendor_id}', [CustomerController::class, 'getMenu']);
    
     // AJAX: simpan pesanan
    Route::post('/bayar', [CustomerController::class, 'bayar']);
    // Endpoint untuk menerima callback dari Midtrans
    Route::post('/midtrans/callback', [PaymentController::class, 'midtransCallback']);
    
    // AJAX: hapus riwayat pesanan session
    Route::post('/hapus-riwayat', [CustomerController::class, 'hapusRiwayat']);
});

