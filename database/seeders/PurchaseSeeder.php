<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $umkm = Umkm::where('name', 'Toko Roti Demo')->firstOrFail();

        $supplier1 = Supplier::updateOrCreate(
            [
                'umkm_id' => $umkm->id,
                'name' => 'Toko Bahan Kue Makmur',
            ],
            [
                'phone' => '081234567890',
                'address' => 'Pasar Induk Blok A No. 12',
            ]
        );

        $supplier2 = Supplier::updateOrCreate(
            [
                'umkm_id' => $umkm->id,
                'name' => 'CV Sembako Jaya',
            ],
            [
                'phone' => '085678901234',
                'address' => 'Jl. Raya Industri No. 45',
            ]
        );

        $ingredients = Ingredient::where('umkm_id', $umkm->id)
            ->get()
            ->keyBy('name');

        $purchases = [
            [
                'supplier_id' => $supplier1->id,
                'ingredient' => 'Tepung Terigu',
                'quantity' => 5,
                'unit_price' => 35000,
                'purchase_date' => '2026-11-01',
                'notes' => 'Pembelian bahan baku tepung',
            ],
            [
                'supplier_id' => $supplier2->id,
                'ingredient' => 'Gula Pasir',
                'quantity' => 8,
                'unit_price' => 25000,
                'purchase_date' => '2026-11-03',
                'notes' => 'Pembelian bahan baku gula',
            ],
            [
                'supplier_id' => null,
                'ingredient' => 'Minyak Goreng',
                'quantity' => 3,
                'unit_price' => 45000,
                'purchase_date' => '2026-11-05',
                'notes' => 'Pembelian langsung di tempat',
            ],
            [
                'supplier_id' => $supplier1->id,
                'ingredient' => 'Telur Ayam',
                'quantity' => 10,
                'unit_price' => 30000,
                'purchase_date' => '2026-11-08',
                'notes' => 'Pembelian telur untuk produksi',
            ],
            [
                'supplier_id' => null,
                'ingredient' => 'Tepung Terigu',
                'quantity' => 4,
                'unit_price' => 35000,
                'purchase_date' => '2026-11-10',
                'notes' => 'Pembelian tambahan tepung',
            ],
            [
                'supplier_id' => $supplier2->id,
                'ingredient' => 'Gula Pasir',
                'quantity' => 12,
                'unit_price' => 24000,
                'purchase_date' => '2026-11-13',
                'notes' => 'Pembelian tambahan gula',
            ],
            [
                'supplier_id' => null,
                'ingredient' => 'Minyak Goreng',
                'quantity' => 6,
                'unit_price' => 45000,
                'purchase_date' => '2026-11-15',
                'notes' => 'Pembelian minyak goreng',
            ],
            [
                'supplier_id' => $supplier1->id,
                'ingredient' => 'Telur Ayam',
                'quantity' => 15,
                'unit_price' => 29000,
                'purchase_date' => '2026-11-18',
                'notes' => 'Pembelian telur mingguan',
            ],
            [
                'supplier_id' => null,
                'ingredient' => 'Tepung Terigu',
                'quantity' => 7,
                'unit_price' => 35000,
                'purchase_date' => '2026-11-20',
                'notes' => 'Pembelian stok tepung',
            ],
            [
                'supplier_id' => $supplier2->id,
                'ingredient' => 'Gula Pasir',
                'quantity' => 20,
                'unit_price' => 23500,
                'purchase_date' => '2026-11-23',
                'notes' => 'Pembelian stok gula',
            ],
        ];

        foreach ($purchases as $item) {
            $ingredient = $ingredients->get($item['ingredient']);

            if (!$ingredient) {
                continue;
            }

            $totalPrice = $item['quantity'] * $item['unit_price'];

            $purchase = Purchase::updateOrCreate(
                [
                    'umkm_id' => $umkm->id,
                    'purchase_date' => $item['purchase_date'],
                    'notes' => $item['notes'],
                ],
                [
                    'supplier_id' => $item['supplier_id'],
                    'ingredient_id' => $ingredient->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                ]
            );

            Expense::updateOrCreate(
                [
                    'umkm_id' => $umkm->id,
                    'purchase_id' => $purchase->id,
                ],
                [
                    'title' => 'Pembelian ' . $ingredient->name,
                    'category' => 'Bahan Baku',
                    'amount' => $totalPrice,
                    'expense_date' => $item['purchase_date'],
                    'description' => $item['notes'],
                ]
            );
        }
    }
}