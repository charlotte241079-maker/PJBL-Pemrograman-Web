<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseRequest;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    // Memastikan akun UMKM sudah disetujui
    private function ensureApprovedUmkm($user)
    {
        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->umkm?->status !== 'approved') {
            abort(403, 'Akun UMKM belum disetujui.');
        }
    }

    // Mengambil pembelian sesuai pemiliknya
    private function findPurchaseForUser($user, $purchaseId)
    {
        if ($user->isAdmin()) {
            return Purchase::findOrFail($purchaseId);
        }

        $this->ensureApprovedUmkm($user);

        return Purchase::where('umkm_id', $user->umkm_id)
            ->findOrFail($purchaseId);
    }

    // Menampilkan daftar pembelian
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login.',
            ], 401);
        }

        if ($user->isAdmin()) {
            $purchases = Purchase::with([
                'umkm',
                'ingredient',
                'supplier',
                'expense',
            ])->latest('purchase_date')->get();

        } elseif ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);

            $purchases = Purchase::where('umkm_id', $user->umkm_id)
                ->with(['ingredient', 'supplier', 'expense'])
                ->latest('purchase_date')
                ->get();

        } else {
            abort(403, 'Akses ditolak.');
        }

        return response()->json([
            'status' => 'success',
            'data' => $purchases,
        ]);
    }

    // Menambahkan pembelian
    public function store(StorePurchaseRequest $request)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $validated = $request->validated();

        // Pastikan bahan baku milik UMKM yang login
        $ingredient = Ingredient::where('umkm_id', $user->umkm_id)
            ->findOrFail($validated['ingredient_id']);

        // Pastikan supplier milik UMKM yang login
        $supplier = null;

        if (!empty($validated['supplier_id'])) {
            $supplier = Supplier::where('umkm_id', $user->umkm_id)
                ->findOrFail($validated['supplier_id']);
        }

        $purchase = DB::transaction(function () use (
            $user,
            $validated,
            $ingredient,
            $supplier
        ) {
            $totalPrice = $validated['quantity'] * $validated['unit_price'];

            // Simpan pembelian melalui relasi UMKM
            $purchase = $user->umkm->purchases()->create([
                'supplier_id' => $supplier?->id,
                'ingredient_id' => $ingredient->id,
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'total_price' => $totalPrice,
                'purchase_date' => $validated['purchase_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Tambahkan stok bahan
            $ingredient->increment('stock', $validated['quantity']);

            $ingredient->update([
                'last_price' => $validated['unit_price'],
            ]);

            // Catat pengeluaran otomatis untuk UMKM yang sama
            $user->umkm->expenses()->create([
                'purchase_id' => $purchase->id,
                'title' => 'Pembelian ' . $ingredient->name .
                    ' (' . $validated['quantity'] . ' ' . $ingredient->unit . ')',
                'category' => 'Bahan Baku',
                'amount' => $totalPrice,
                'expense_date' => $validated['purchase_date'],
                'description' => $validated['notes']
                    ?? 'Otomatis dari transaksi pembelian',
            ]);

            return $purchase->load([
                'ingredient',
                'supplier',
                'expense',
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pembelian berhasil dicatat, stok bertambah dan pengeluaran diperbarui.',
            'data' => $purchase,
        ], 201);
    }

    // Menampilkan detail pembelian
    public function show(Request $request, Purchase $purchase)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login.',
            ], 401);
        }

        $purchase = $this->findPurchaseForUser($user, $purchase->id);

        return response()->json([
            'status' => 'success',
            'data' => $purchase->load([
                'ingredient',
                'supplier',
                'expense',
            ]),
        ]);
    }

    // Memperbarui tanggal dan catatan pembelian
    public function update(Request $request, Purchase $purchase)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $purchase = $this->findPurchaseForUser($user, $purchase->id);

        $validated = $request->validate([
            'purchase_date' => 'sometimes|required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $purchase->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pembelian berhasil diperbarui.',
            'data' => $purchase->fresh([
                'ingredient',
                'supplier',
                'expense',
            ]),
        ]);
    }

    // Menghapus pembelian dan mengembalikan stok
    public function destroy(Request $request, Purchase $purchase)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $purchase = $this->findPurchaseForUser($user, $purchase->id);

        DB::transaction(function () use ($purchase, $user) {

            // Pastikan bahan baku juga milik UMKM yang sama
            $ingredient = Ingredient::where('umkm_id', $user->umkm_id)
                ->findOrFail($purchase->ingredient_id);

            // Hapus pengeluaran otomatis yang terkait
            Expense::where('purchase_id', $purchase->id)
                ->where('umkm_id', $user->umkm_id)
                ->delete();

            // Kembalikan stok sesuai jumlah pembelian
            $ingredient->decrement('stock', $purchase->quantity);

            $purchase->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pembelian dihapus, stok dikembalikan, dan pengeluaran terkait dihapus.',
        ]);
    }
}
