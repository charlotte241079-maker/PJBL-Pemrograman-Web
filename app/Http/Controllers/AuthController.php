<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'business_type' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $umkm = Umkm::create([
                'name' => $validated['business_name'],
                'owner_name' => $validated['owner_name'],
                'business_type' => $validated['business_type'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'pending',
            ]);

            User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'umkm',
                'umkm_id' => $umkm->id,
            ]);
        });

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil. Silakan tunggu persetujuan Admin.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isUmkm()) {
            $status = $user->umkm?->status;

            if ($status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('account.pending');
            }

            if ($status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('account.rejected');
            }

            if ($status === 'approved') {
                return redirect()->intended(route('dashboard'));
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withErrors(['email' => 'Akun tidak memiliki akses yang valid.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        if ($user->isUmkm()) {
            $status = $user->umkm?->status;

            if ($status === 'pending') {
                return redirect()->route('account.pending');
            }

            if ($status === 'rejected') {
                return redirect()->route('account.rejected');
            }

            if ($status === 'approved') {
                return redirect()->route('dashboard');
            }
        }

        Auth::logout();

        return redirect()->route('login');
    }
}
