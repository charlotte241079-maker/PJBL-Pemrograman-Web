<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        // Pastikan pengguna sudah login dan memiliki role yang sesuai
        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        // Khusus akun Pelaku UMKM
        if ($user->isUmkm()) {
            $status = $user->umkm?->status;

            if ($status === 'pending') {
                return redirect()->route('account.pending');
            }

            if ($status === 'rejected') {
                return redirect()->route('account.rejected');
            }

            if ($status !== 'approved') {
                abort(403, 'Akun UMKM belum memiliki akses.');
            }
        }

        return $next($request);
    }
}
