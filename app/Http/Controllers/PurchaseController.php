<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'ingredient']);

        // Pencarian berdasarkan nama bahan, supplier, atau catatan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('ingredient', fn($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter berdasarkan Supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->paginate(10)->withQueryString();
        $suppliers = Supplier::all();
        $ingredients = Ingredient::all();

        // Deteksi bahan dengan stok kritis (stok <= 5)
        $lowStockIngredients = Ingredient::where('stock', '<=', 5)->get();

        return view('pembelian.index', compact('purchases', 'suppliers', 'ingredients', 'lowStockIngredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date'   => 'required|date',
            'supplier_id'     => 'required|exists:suppliers,id',
            'ingredient_ids'  => 'required|array',
            'quantities'      => 'required|array',
            'unit_prices'     => 'required|array',
            'notes'           => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->ingredient_ids as $index => $ingredientId) {
                if (!empty($ingredientId)) {
                    $qty = (float) ($request->quantities[$index] ?? 1);
                    $unitPrice = (float) ($request->unit_prices[$index] ?? 0);
                    $totalPrice = $qty * $unitPrice;

                    // 1. Simpan ke tabel purchases
                    $purchase = Purchase::create([
                        'supplier_id'   => $request->supplier_id,
                        'ingredient_id' => $ingredientId,
                        'quantity'      => $qty,
                        'unit_price'    => $unitPrice,
                        'total_price'   => $totalPrice,
                        'purchase_date' => $request->purchase_date,
                        'notes'         => $request->notes,
                    ]);

                    // 2. Otomatis tambah stok di tabel ingredients
                    $ingredient = Ingredient::find($ingredientId);
                    if ($ingredient) {
                        $ingredient->increment('stock', $qty);
                    }

                    // 3. Otomatis catat ke tabel expenses agar muncul di Rekap Laporan
                    Expense::create([
                        'purchase_id'  => $purchase->id,
                        'title'        => 'Pembelian ' . ($ingredient->name ?? 'Bahan Baku') . " ({$qty} " . ($ingredient->unit ?? 'unit') . ')',
                        'category'     => 'Bahan Baku',
                        'amount'       => $totalPrice,
                        'expense_date' => $request->purchase_date,
                        'description'  => $request->notes ?? 'Restock bahan dari supplier',
                    ]);
                }
            }
        });

        return redirect()->route('pembelian.index')
            ->with('success', 'Transaksi berhasil disimpan, stok bahan bertambah, dan laporan keuangan otomatis diperbarui!');
    }
}