<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Purchase;
use App\Exports\LaporanExport;
use App\Imports\LaporanImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data Pengeluaran dari tabel expenses (yang bukan duplikat dari purchase_id)
        $expenses = Expense::whereNull('purchase_id')
            ->when($request->filled('search'), fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('expense_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('expense_date', '<=', $request->end_date))
            ->get()
            ->map(function ($item) {
                $item->jenis = 'Pengeluaran';
                $item->tanggal = $item->expense_date;
                $item->judul = $item->title;
                $item->kategori = $item->category;
                $item->nominal = $item->amount;
                $item->keterangan = $item->description;
                return $item;
            });

        // 2. Ambil data Pembelian dari tabel purchases (Pemasukan)
        $purchases = Purchase::with(['ingredient', 'supplier'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('ingredient', fn($iq) => $iq->where('name', 'like', "%{$search}%"))
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                if ($request->category !== 'Pembelian' && $request->category !== 'Bahan Baku') {
                    $q->whereRaw('1 = 0');
                }
            })
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('purchase_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('purchase_date', '<=', $request->end_date))
            ->get()
            ->map(function ($item) {
                $namaBahan = $item->ingredient->name ?? 'Bahan Baku';
                $unit = $item->ingredient->unit ?? 'unit';
                $supplier = $item->supplier->name ?? '-';
                $item->jenis = 'Pemasukan';
                $item->tanggal = $item->purchase_date;
                $item->judul = "Pembelian {$namaBahan} ({$item->quantity} {$unit})";
                $item->kategori = 'Pembelian';
                $item->nominal = $item->total_price;
                $item->keterangan = ($item->notes ? $item->notes . ' - ' : '') . "Supplier: {$supplier}";
                return $item;
            });

        // 3. Gabungkan keduanya & urutkan berdasarkan tanggal terbaru
        $merged = $expenses->concat($purchases)->sortByDesc('tanggal')->values();

        // 4. Buat Pagination manual untuk hasil gabungan
        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $data = new LengthAwarePaginator(
            $merged->forPage($currentPage, $perPage),
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('rekap-laporan.index', compact('data'));
    }

    public function importExportPage()
    {
        $totalData = Expense::count() + Purchase::count();
        $recentData = Expense::orderBy('expense_date', 'desc')->take(5)->get();
        return view('import-export.index', compact('totalData', 'recentData'));
    }

    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'category', 'start_date', 'end_date', 'sort_dir']);
        return Excel::download(new LaporanExport($filters), 'laporan-keuangan-umkm.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new LaporanImport, $request->file('file'));

        return back()->with('success', 'Data Excel berhasil diimport!');
    }

    public function exportPdf(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        $sortDir = $request->get('sort_dir', 'desc');
        $data = $query->orderBy('expense_date', $sortDir)->get();

        $pdf = Pdf::loadView('reports.pdf', compact('data'));
        return $pdf->download('laporan-keuangan-umkm.pdf');
    }
}