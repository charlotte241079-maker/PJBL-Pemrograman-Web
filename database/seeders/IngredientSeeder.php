<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $umkm = Umkm::where('name', 'Toko Roti Demo')->firstOrFail();

        $ingredients = [
            [
                'name' => 'Tepung Terigu',
                'category' => 'Bahan Kering',
                'stock' => 5,
                'unit' => 'kg',
                'min_stock' => 10,
                'last_price' => 14000,
            ],
            [
                'name' => 'Gula Pasir',
                'category' => 'Pemanis',
                'stock' => 20,
                'unit' => 'kg',
                'min_stock' => 5,
                'last_price' => 17000,
            ],
            [
                'name' => 'Minyak Goreng',
                'category' => 'Minyak',
                'stock' => 3,
                'unit' => 'liter',
                'min_stock' => 5,
                'last_price' => 18000,
            ],
            [
                'name' => 'Telur Ayam',
                'category' => 'Protein',
                'stock' => 2,
                'unit' => 'kg',
                'min_stock' => 5,
                'last_price' => 28000,
            ],
            [
                'name' => 'Cokelat Bubuk',
                'category' => 'Bahan Kue',
                'stock' => 8,
                'unit' => 'kg',
                'min_stock' => 3,
                'last_price' => 45000,
            ],
            [
                'name' => 'Susu UHT',
                'category' => 'Produk Susu',
                'stock' => 12,
                'unit' => 'liter',
                'min_stock' => 5,
                'last_price' => 19000,
            ],
            [
                'name' => 'Keju',
                'category' => 'Produk Susu',
                'stock' => 1,
                'unit' => 'kg',
                'min_stock' => 3,
                'last_price' => 65000,
            ],
            [
                'name' => 'Kopi Bubuk',
                'category' => 'Minuman',
                'stock' => 10,
                'unit' => 'kg',
                'min_stock' => 4,
                'last_price' => 55000,
            ],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::updateOrCreate(
                [
                    'umkm_id' => $umkm->id,
                    'name' => $ingredient['name'],
                ],
                $ingredient
            );
        }
    }
}