<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Otomatis jalankan migrasi & buat akun admin jika tabel users belum ada
        try {
            if (!Schema::hasTable('users')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--force' => true]);
            }

            if (Schema::hasTable('users') && !User::where('email', 'admin@umkmhub.id')->exists()) {
                User::create([
                    'name' => 'Admin Juragan',
                    'email' => 'admin@umkmhub.id',
                    'password' => Hash::make('admin123'),
                ]);
            }
        } catch (\Throwable $e) {
            // Biarkan error DB asli tertangkap normal jika koneksi memang bermasalah
        }
    }
}