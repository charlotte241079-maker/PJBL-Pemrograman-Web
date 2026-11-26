<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminUmkmController extends Controller
{
    // Menampilkan pendaftaran UMKM yang menunggu persetujuan
    public function pendaftaran(): View
    {
        $pendaftaran = Umkm::where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.pendaftaran.index', compact('pendaftaran'));
    }

    // Menyetujui pendaftaran UMKM
    public function approve(Umkm $umkm): RedirectResponse
    {
        if ($umkm->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $umkm->update([
            'status' => 'approved',
        ]);

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Pendaftaran UMKM berhasil disetujui.');
    }

    // Menolak pendaftaran UMKM
    public function reject(Umkm $umkm): RedirectResponse
    {
        if ($umkm->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah diproses.');
        }

        $umkm->update([
            'status' => 'rejected',
        ]);

        return redirect()
            ->route('admin.pendaftaran.index')
            ->with('success', 'Pendaftaran UMKM berhasil ditolak.');
    }

    // Menampilkan seluruh data UMKM
    public function index(): View
    {
        $umkms = Umkm::withCount([
            'users',
            'suppliers',
            'ingredients',
            'purchases',
            'expenses',
        ])->latest()->get();

        return view('admin.umkm.index', compact('umkms'));
    }
}
