<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Expense;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Pastikan hanya Admin yang dapat membuka dashboard.
        abort_unless(
            auth()->user() && auth()->user()->isAdmin(),
            403,
            'Anda tidak memiliki akses ke halaman Admin.'
        );

        // STATISTIK PENDAFTARAN UMKM
        $totalUmkm = Umkm::count();

        $totalPending = Umkm::where('status', 'pending')->count();

        $totalApproved = Umkm::where('status', 'approved')->count();

        $totalRejected = Umkm::where('status', 'rejected')->count();

        $umkmTerbaru = Umkm::latest()
            ->take(5)
            ->get();

        // STATISTIK OPERASIONAL SELURUH UMKM
        $totalJenisBahan = Ingredient::count();

        $totalStok = Ingredient::sum('stock');

        $totalPembelian = Purchase::count();

        $totalPengeluaran = Expense::sum('amount');

        // LIMA PENGELUARAN TERBARU
        $pengeluaranTerbaru = Expense::with('umkm')
            ->latest('expense_date')
            ->latest('id')
            ->take(5)
            ->get();

        // LIMA PEMBELIAN TERBARU
        $pembelianTerbaru = Purchase::with([
            'umkm',
            'supplier',
            'ingredient'
        ])
            ->latest('purchase_date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUmkm',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'umkmTerbaru',
            'totalJenisBahan',
            'totalStok',
            'totalPembelian',
            'totalPengeluaran',
            'pengeluaranTerbaru',
            'pembelianTerbaru'
        ));
    }
}
