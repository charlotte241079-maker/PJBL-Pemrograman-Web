
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUmkmController;

// ========================================
// HALAMAN AWAL
// ========================================

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    if (Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
});

// ========================================
// LOGIN DAN REGISTRASI
// ========================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post');

});

// ========================================
// STATUS PENDAFTARAN
// ========================================

Route::view('/pendaftaran/pending', 'auth.pending')
    ->name('account.pending');

Route::view('/pendaftaran/rejected', 'auth.rejected')
    ->name('account.rejected');

// ========================================
// LOGOUT
// ========================================

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ========================================
// ROUTE KHUSUS ADMIN
// ========================================

Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Admin hanya dapat melihat pembelian
    Route::get('/admin/pembelian', [PurchaseController::class, 'index'])
        ->name('admin.pembelian.index');

    // Admin hanya dapat melihat pengeluaran
    Route::view('/admin/pengeluaran', 'pengeluaran.index')
        ->name('admin.pengeluaran.index');
    
    // Manajemen UMKM

// Persetujuan pendaftaran
    Route::get('/admin/pendaftaran', [AdminUmkmController::class, 'pendaftaran'])
        ->name('admin.pendaftaran.index');

    Route::post('/admin/pendaftaran/{umkm}/approve', [AdminUmkmController::class, 'approve'])
        ->name('admin.pendaftaran.approve');

    Route::post('/admin/pendaftaran/{umkm}/reject', [AdminUmkmController::class, 'reject'])
        ->name('admin.pendaftaran.reject');

// Data seluruh UMKM
    Route::get('/admin/umkm', [AdminUmkmController::class, 'index'])
        ->name('admin.umkm.index');
});


// ========================================
// ROUTE KHUSUS UMKM
// ========================================

Route::middleware(['auth', 'role:umkm'])->group(function () {

    // Dashboard
    Route::view('/dashboard', 'dashboard.index')
        ->name('dashboard');

    // Stok bahan
    Route::view('/stok-bahan', 'stok-bahan.index')
        ->name('stok-bahan.index');

    // Pembelian
    Route::get('/pembelian', [PurchaseController::class, 'index'])
        ->name('pembelian.index');

    Route::post('/pembelian', [PurchaseController::class, 'store'])
        ->name('pembelian.store');

    // Pengeluaran
    Route::view('/pengeluaran', 'pengeluaran.index')
        ->name('pengeluaran.index');

    // Analisis data
    Route::view('/analisis-data', 'analisis-data.index')
        ->name('analisis-data.index');

    // Rekap laporan
    Route::get('/rekap-laporan', [ReportController::class, 'index'])
        ->name('rekap-laporan.index');

    // Import / Export
    Route::get('/import-export', [ReportController::class, 'importExportPage'])
        ->name('import-export.index');

    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel');

    Route::post('/reports/import-excel', [ReportController::class, 'importExcel'])
        ->name('reports.import.excel');

    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export.pdf');

    // Catatan
    Route::get('/catatan', [NoteController::class, 'index'])
        ->name('notes.index');

    Route::post('/catatan', [NoteController::class, 'store'])
        ->name('notes.store');

    Route::delete('/catatan/{note}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');

});
