<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Purchase;
use Illuminate\Http\Request;


class IngredientController extends Controller
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

    // Mengambil bahan sesuai hak akses pengguna
    private function findIngredientForUser($user, $ingredientId)
    {
        if ($user->isAdmin()) {
            return Ingredient::findOrFail($ingredientId);
        }

        $this->ensureApprovedUmkm($user);

        return Ingredient::where('umkm_id', $user->umkm_id)
            ->findOrFail($ingredientId);
    }

    // Menampilkan daftar bahan
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
            $ingredients = Ingredient::with('umkm')
                ->orderBy('name')
                ->get();
        } elseif ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);

            $ingredients = Ingredient::where('umkm_id', $user->umkm_id)
                ->orderBy('name')
                ->get();
        } else {
            abort(403, 'Akses ditolak.');
        }

        return response()->json([
            'status' => 'success',
            'data' => $ingredients,
        ], 200);
    }

    // Menambahkan bahan
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'stock' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'last_price' => 'nullable|numeric|min:0',
        ]);

        $ingredient = $user->umkm->ingredients()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Bahan baku berhasil ditambahkan.',
            'data' => $ingredient,
        ], 201);
    }

    // Menampilkan detail bahan
    public function show(Request $request, Ingredient $ingredient)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login.',
            ], 401);
        }

        $ingredient = $this->findIngredientForUser(
            $user,
            $ingredient->id
        );

        return response()->json([
            'status' => 'success',
            'data' => $ingredient,
        ], 200);
    }

    // Memperbarui bahan
    public function update(Request $request, Ingredient $ingredient)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $ingredient = $this->findIngredientForUser(
            $user,
            $ingredient->id
        );

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:100',
            'stock' => 'nullable|numeric|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'min_stock' => 'nullable|numeric|min:0',
            'last_price' => 'nullable|numeric|min:0',
        ]);

        $ingredient->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data bahan berhasil diperbarui.',
            'data' => $ingredient->fresh(),
        ], 200);
    }

    // Menghapus bahan
        public function destroy(Request $request, Ingredient $ingredient)
        {
            $user = $request->user();

            if (!$user || !$user->isUmkm()) {
                abort(403, 'Akses ditolak.');
            }

            $this->ensureApprovedUmkm($user);

            $ingredient = $this->findIngredientForUser(
                $user,
                $ingredient->id
            );

            $hasPurchases = Purchase::where('ingredient_id', $ingredient->id)
                ->where('umkm_id', $user->umkm_id)
                ->exists();

            if ($hasPurchases) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bahan tidak dapat dihapus karena sudah digunakan dalam transaksi pembelian.',
                ], 409);
            }

            $ingredient->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Bahan berhasil dihapus.',
                ]);
            }
    // Statistik bahan untuk dashboard
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login.',
            ], 401);
        }

        if ($user->isAdmin()) {
            $ingredients = Ingredient::all();
        } elseif ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);

            $ingredients = Ingredient::where('umkm_id', $user->umkm_id)
                ->get();
        } else {
            abort(403, 'Akses ditolak.');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_jenis_bahan' => $ingredients->count(),

                'total_stok' => $ingredients->sum('stock'),

                'stok_menipis' => $ingredients->filter(function ($item) {
                    return $item->stock <= $item->min_stock;
                })->count(),

                'total_nilai_persediaan' => $ingredients->sum(function ($item) {
                    return $item->stock * $item->last_price;
                }),
            ],
        ]);
    }
}
