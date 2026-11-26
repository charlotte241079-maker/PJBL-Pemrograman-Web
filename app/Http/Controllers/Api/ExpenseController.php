<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Memastikan akun UMKM sudah disetujui.
     */
    private function ensureApprovedUmkm($user): void
    {
        abort_unless(
            $user && $user->isUmkm(),
            403,
            'Akses hanya untuk akun UMKM.'
        );

        abort_unless(
            $user->umkm && $user->umkm->status === 'approved',
            403,
            'Akun UMKM belum disetujui.'
        );
    }

    /**
     * Mengambil pengeluaran sesuai hak akses pengguna.
     */
    private function findExpenseForUser($user, int $expenseId): Expense
    {
        if ($user->isAdmin()) {
            return Expense::findOrFail($expenseId);
        }

        return Expense::where('umkm_id', $user->umkm_id)
            ->findOrFail($expenseId);
    }

    /**
     * Menampilkan daftar dan ringkasan pengeluaran.
     */
    public function index()
    {
        $user = Auth::user();

        abort_unless($user, 401, 'Silakan login terlebih dahulu.');

        $query = Expense::query();

        if ($user->isAdmin()) {
            // Admin dapat memantau seluruh pengeluaran.
            $expenses = (clone $query)
                ->with(['purchase', 'umkm'])
                ->latest('expense_date')
                ->get();

        } elseif ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);

            $query->where('umkm_id', $user->umkm_id);

            $expenses = (clone $query)
                ->with('purchase')
                ->latest('expense_date')
                ->get();

        } else {
            abort(403, 'Akses ditolak.');
        }

        $summary = [
            'total_transaksi' => (clone $query)->count(),

            'total_pengeluaran' => (clone $query)
                ->sum('amount'),

            'pengeluaran_bulan_ini' => (clone $query)
                ->whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),

            'update_terakhir' => (clone $query)
                ->latest('updated_at')
                ->value('updated_at'),
        ];

        return response()->json([
            'status' => 'success',
            'summary' => $summary,
            'data' => $expenses,
        ]);
    }

    /**
     * Menambahkan pengeluaran manual.
     */
    public function store(StoreExpenseRequest $request)
    {
        $user = Auth::user();

        $this->ensureApprovedUmkm($user);

        $validated = $request->validated();

        // Jangan menerima umkm_id dari input pengguna.
        unset($validated['umkm_id']);

        // Pengeluaran manual tidak boleh dikaitkan
        // ke pembelian melalui endpoint ini.
        abort_if(
            !empty($validated['purchase_id']),
            422,
            'Pengeluaran pembelian dibuat otomatis melalui transaksi pembelian.'
        );

        $expense = $user->umkm->expenses()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengeluaran berhasil dicatat.',
            'data' => $expense,
        ], 201);
    }

    /**
     * Menampilkan detail pengeluaran.
     */
    public function show(Expense $expense)
    {
        $user = Auth::user();

        abort_unless($user, 401, 'Silakan login terlebih dahulu.');

        if ($user->isUmkm()) {
            $this->ensureApprovedUmkm($user);
        } else {
            abort_unless($user->isAdmin(), 403, 'Akses ditolak.');
        }

        $expense = $this->findExpenseForUser($user, $expense->id);

        return response()->json([
            'status' => 'success',
            'data' => $expense->load(['purchase', 'umkm']),
        ]);
    }

    /**
     * Mengubah pengeluaran manual.
     */
    public function update(Request $request, Expense $expense)
    {
        $user = Auth::user();

        $this->ensureApprovedUmkm($user);

        $expense = $this->findExpenseForUser($user, $expense->id);

        abort_if(
            $expense->purchase_id !== null,
            403,
            'Pengeluaran otomatis dari pembelian tidak dapat diubah di sini.'
        );

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:100',
            'amount' => 'sometimes|required|numeric|gt:0',
            'expense_date' => 'sometimes|required|date',
            'description' => 'sometimes|nullable|string',
        ]);

        $expense->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengeluaran berhasil diperbarui.',
            'data' => $expense,
        ]);
    }

    /**
     * Menghapus pengeluaran manual.
     */
    public function destroy(Expense $expense)
    {
        $user = Auth::user();

        $this->ensureApprovedUmkm($user);

        $expense = $this->findExpenseForUser($user, $expense->id);

        abort_if(
            $expense->purchase_id !== null,
            403,
            'Pengeluaran otomatis dihapus melalui transaksi pembelian.'
        );

        $expense->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengeluaran berhasil dihapus.',
        ]);
    }
}
