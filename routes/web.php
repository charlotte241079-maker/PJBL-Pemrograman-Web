
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PurchaseController;

Route::view('/', 'dashboard.index')->name('dashboard');
Route::view('/dashboard', 'dashboard.index');
Route::view('/stok-bahan', 'stok-bahan.index');
Route::view('/pembelian', 'pembelian.index');
Route::view('/pengeluaran', 'pengeluaran.index');
Route::view('/analisis-data', 'analisis-data.index');

// Bagian Anggota 3
Route::get('/rekap-laporan', [ReportController::class, 'index'])->name('rekap-laporan.index');
Route::get('/import-export', [ReportController::class, 'importExportPage'])->name('import-export.index');
Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
Route::post('/reports/import-excel', [ReportController::class, 'importExcel'])->name('reports.import.excel');
Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

// Bagian Catatan
Route::get('/catatan', [NoteController::class, 'index'])->name('notes.index');
Route::post('/catatan', [NoteController::class, 'store'])->name('notes.store');
Route::delete('/catatan/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

Route::get('/pembelian', [PurchaseController::class, 'index'])->name('pembelian.index');
Route::post('/pembelian', [PurchaseController::class, 'store'])->name('pembelian.store');