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
    // Memastikan akun UMKM sudah disetujui.
    private function ensureApprovedUmkm($user): void
    {
        abort_unless(
            $user && $user->isUmkm(),
            403,
            'Akses ditolak.'
        );

        abort_unless(
            $user->umkm && $user->umkm->status === 'approved',
            403,
            'Akun UMKM belum disetujui.'
        );
    }

    // Menampilkan daftar pembelian.
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401, 'Silakan login.');

        $query = Purchase::with(['supplier', 'ingredient']);

        if ($user->isAdmin()) {
            // Admin dapat memantau seluruh transaksi.
            $suppliers = Supplier::all();
            $ingredients = Ingredient::all();

            $lowStockIngredients = Ingredient::where(
                'stock',
                '<=',
                5
            )->get();

        } elseif ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);

            // Batasi transaksi berdasarkan UMKM yang login.
            $query->where('umkm_id', $user->umkm_id);

            $suppliers = Supplier::where(
                'umkm_id',
                $user->umkm_id
            )->get();

            $ingredients = Ingredient::where(
                'umkm_id',
                $user->umkm_id
            )->get();

            $lowStockIngredients = Ingredient::where(
                'umkm_id',
                $user->umkm_id
            )->where('stock', '<=', 5)->get();

        } else {
            abort(403, 'Akses ditolak.');
        }

        // Pencarian nama bahan, supplier, atau catatan.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ingredient', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter supplier.
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchases = $query
            ->orderBy('purchase_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pembelian.index', compact(
            'purchases',
            'suppliers',
            'ingredients',
            'lowStockIngredients'
        ));
    }

    // Menambahkan transaksi pembelian.
    public function store(Request $request)
    {
        $user = $request->user();

        // Admin hanya boleh memantau, bukan menambah transaksi.
        $this->ensureApprovedUmkm($user);

        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'required|integer|exists:suppliers,id',

            'ingredient_ids' => 'required|array|min:1',
            'ingredient_ids.*' => 'required|integer|exists:ingredients,id',

            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:0.01',

            'unit_prices' => 'required|array',
            'unit_prices.*' => 'required|numeric|min:0',

            'notes' => 'nullable|string|max:500',
        ]);

        // Pastikan supplier milik UMKM yang sedang login.
        $supplier = Supplier::where('umkm_id', $user->umkm_id)
            ->findOrFail($validated['supplier_id']);

        // Pastikan setiap bahan baku milik UMKM yang sama.
        foreach ($validated['ingredient_ids'] as $ingredientId) {
            Ingredient::where('umkm_id', $user->umkm_id)
                ->findOrFail($ingredientId);
        }

        // Pastikan jumlah dan harga tersedia untuk setiap bahan.
        if (
            count($validated['ingredient_ids']) !== count($validated['quantities']) ||
            count($validated['ingredient_ids']) !== count($validated['unit_prices'])
        ) {
            return back()
                ->withErrors(['ingredient_ids' => 'Data bahan, jumlah, dan harga tidak sesuai.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $user, $supplier) {
            foreach ($validated['ingredient_ids'] as $index => $ingredientId) {

                $qty = (float) $validated['quantities'][$index];
                $unitPrice = (float) $validated['unit_prices'][$index];
                $totalPrice = $qty * $unitPrice;

                // Ambil bahan yang benar-benar milik UMKM.
                $ingredient = Ingredient::where(
                    'umkm_id',
                    $user->umkm_id
                )->findOrFail($ingredientId);

                // Simpan transaksi dengan umkm_id dari akun login.
                $purchase = $user->umkm->purchases()->create([
                    'supplier_id' => $supplier->id,
                    'ingredient_id' => $ingredient->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'purchase_date' => $validated['purchase_date'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Perbarui stok dan harga terakhir.
                $ingredient->increment('stock', $qty);
                $ingredient->update([
                    'last_price' => $unitPrice,
                ]);

                // Catat pengeluaran otomatis untuk UMKM yang sama.
                $user->umkm->expenses()->create([
                    'purchase_id' => $purchase->id,
                    'title' => 'Pembelian ' . $ingredient->name
                        . " ({$qty} " . $ingredient->unit . ')',
                    'category' => 'Bahan Baku',
                    'amount' => $totalPrice,
                    'expense_date' => $validated['purchase_date'],
                    'description' => $validated['notes']
                        ?? 'Restock bahan dari supplier',
                ]);
            }
        });

        return redirect()
            ->route('pembelian.index')
            ->with(
                'success',
                'Transaksi berhasil disimpan, stok bahan bertambah, dan laporan keuangan otomatis diperbarui!'
            );
    }
}
