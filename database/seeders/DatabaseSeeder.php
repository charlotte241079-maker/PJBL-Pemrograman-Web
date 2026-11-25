<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Admin
        User::updateOrCreate(
            ['email' => 'admin@umkmhub.id'],
            [
                'name' => 'Admin Juragan',
                'password' => Hash::make('bekasitanparaiapasih'),
            ]
        );

        // 2. Data Supplier
        Supplier::updateOrCreate(
            ['name' => 'Toko Bahan Kue Makmur'],
            [
                'phone' => '081234567890',
                'address' => 'Pasar Induk Blok A No. 12',
            ]
        );

        Supplier::updateOrCreate(
            ['name' => 'CV Sembako Jaya'],
            [
                'phone' => '085678901234',
                'address' => 'Jl. Raya Industri No. 45',
            ]
        );

        // 3. Data Ingredient
        Ingredient::updateOrCreate(
            ['name' => 'Tepung Terigu Protein Sedang'],
            [
                'category' => 'Bahan Kering',
                'stock' => 25,
                'unit' => 'kg',
                'min_stock' => 5,
                'last_price' => 12000,
            ]
        );

        Ingredient::updateOrCreate(
            ['name' => 'Gula Pasir Putih'],
            [
                'category' => 'Bahan Kering',
                'stock' => 15,
                'unit' => 'kg',
                'min_stock' => 5,
                'last_price' => 16500,
            ]
        );

        Ingredient::updateOrCreate(
            ['name' => 'Minyak Goreng Kemasan'],
            [
                'category' => 'Bahan Cair',
                'stock' => 10,
                'unit' => 'liter',
                'min_stock' => 3,
                'last_price' => 15000,
            ]
        );

        // 4. Jalankan Seeder Lainnya
        $this->call([
            ExpenseSeeder::class,
            PurchaseSeeder::class,
            LaporanSeeder::class,
        ]);
    }
}