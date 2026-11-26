<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Memeriksa apakah UMKM sudah disetujui
    private function ensureApprovedUmkm($user)
    {
        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        if ($user->umkm?->status !== 'approved') {
            abort(403, 'Akun UMKM belum disetujui.');
        }
    }

    // Mengambil supplier sesuai pemiliknya
    private function findSupplierForUser($user, $supplierId)
    {
        if ($user->isAdmin()) {
            return Supplier::findOrFail($supplierId);
        }

        $this->ensureApprovedUmkm($user);

        return Supplier::where('umkm_id', $user->umkm_id)
            ->findOrFail($supplierId);
    }

    // Menampilkan daftar supplier
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
            $suppliers = Supplier::with('umkm')
                ->latest()
                ->get();
        } elseif ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);

            $suppliers = Supplier::where('umkm_id', $user->umkm_id)
                ->latest()
                ->get();
        } else {
            abort(403, 'Akses ditolak.');
        }

        return response()->json([
            'status' => 'success',
            'data' => $suppliers,
        ]);
    }

    // Menambahkan supplier
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $supplier = $user->umkm->suppliers()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier berhasil ditambahkan.',
            'data' => $supplier,
        ], 201);
    }

    // Menampilkan detail supplier
    public function show(Request $request, Supplier $supplier)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus login.',
            ], 401);
        }

        $supplier = $this->findSupplierForUser($user, $supplier->id);

        $supplier->load([
            'purchases' => function ($query) use ($supplier) {
                $query->where('umkm_id', $supplier->umkm_id);
            },
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $supplier,
        ]);
    }

    // Memperbarui supplier
    public function update(Request $request, Supplier $supplier)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $supplier = $this->findSupplierForUser($user, $supplier->id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data supplier berhasil diperbarui.',
            'data' => $supplier,
        ]);
    }

    // Menghapus supplier
    public function destroy(Request $request, Supplier $supplier)
    {
        $user = $request->user();

        if (!$user || !$user->isUmkm()) {
            abort(403, 'Akses ditolak.');
        }

        $this->ensureApprovedUmkm($user);

        $supplier = $this->findSupplierForUser($user, $supplier->id);

        $supplier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier berhasil dihapus.',
        ]);
    }
}
