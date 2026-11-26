<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Password diambil dari file .env, bukan ditulis di kode.
        $adminPassword = env('ADMIN_PASSWORD');
        $umkmPassword = env('DEMO_UMKM_PASSWORD');

        if (!$adminPassword || !$umkmPassword) {
            throw new RuntimeException(
                'ADMIN_PASSWORD dan DEMO_UMKM_PASSWORD harus diisi di file .env.'
            );
        }

        // Membuat akun Admin.
        User::updateOrCreate(
            ['email' => 'admin@umkmhub.id'],
            [
                'name' => 'Admin UMKM Hub',
                'password' => Hash::make($adminPassword),
                'role' => 'admin',
                'umkm_id' => null,
            ]
        );

        // Membuat satu UMKM contoh yang sudah disetujui.
        $umkm = Umkm::updateOrCreate(
            ['name' => 'Toko Roti Demo'],
            [
                'owner_name' => 'Pemilik Toko Roti Demo',
                'business_type' => 'Kuliner',
                'phone' => '081234567890',
                'address' => 'Bogor, Jawa Barat',
                'status' => 'approved',
            ]
        );

        // Membuat akun Pelaku UMKM untuk data contoh.
        User::updateOrCreate(
            ['email' => 'umkm@umkmhub.id'],
            [
                'name' => 'Pemilik Toko Roti Demo',
                'password' => Hash::make($umkmPassword),
                'role' => 'umkm',
                'umkm_id' => $umkm->id,
            ]
        );

        // Data operasional dibuat setelah UMKM tersedia.
        $this->call([
            IngredientSeeder::class,
            PurchaseSeeder::class,
            ExpenseSeeder::class,
            LaporanSeeder::class,
        ]);
    }
}