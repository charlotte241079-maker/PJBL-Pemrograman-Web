<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $supplier1 = Supplier::firstOrCreate(
            ['name' => 'Toko Bahan Kue Makmur'],
            [
                'phone' => '081234567890',
                'address' => 'Pasar Induk Blok A No. 12',
            ]
        );

        $supplier2 = Supplier::firstOrCreate(
            ['name' => 'CV Sembako Jaya'],
            [
                'phone' => '085678901234',
                'address' => 'Jl. Raya Industri No. 45',
            ]
        );

        $ingredient1 = Ingredient::firstOrCreate(
            ['name' => 'Tepung Terigu Protein Sedang'],
            [
                'category' => 'Bahan Baku',
                'unit' => 'kg',
            ]
        );

        $ingredient2 = Ingredient::firstOrCreate(
            ['name' => 'Gula Pasir'],
            [
                'category' => 'Bahan Baku',
                'unit' => 'kg',
            ]
        );

        $ingredient3 = Ingredient::firstOrCreate(
            ['name' => 'Minyak Goreng'],
            [
                'category' => 'Bahan Baku',
                'unit' => 'liter',
            ]
        );

        $ingredient4 = Ingredient::firstOrCreate(
            ['name' => 'Telur Ayam'],
            [
                'category' => 'Bahan Baku',
                'unit' => 'kg',
            ]
        );

        $purchases = [
            [
                'supplier_id' => $supplier1->id,
                'ingredient_id' => $ingredient1->id,
                'quantity' => 5.00,
                'unit_price' => 35000.00,
                'purchase_date' => '2026-11-01',
                'notes' => 'Pembeli: Ibu Rina - Pesanan 5 box (Lunas)',
            ],
            [
                'supplier_id' => $supplier2->id,
                'ingredient_id' => $ingredient2->id,
                'quantity' => 8.00,
                'unit_price' => 25000.00,
                'purchase_date' => '2026-11-03',
                'notes' => 'Pembeli: Pak Budi - Order harian toko',
            ],
            [
                'supplier_id' => null,
                'ingredient_id' => $ingredient3->id,
                'quantity' => 3.00,
                'unit_price' => 45000.00,
                'purchase_date' => '2026-11-05',
                'notes' => 'Pembeli: Nabila - Pembelian langsung di tempat',
            ],
            [
                'supplier_id' => $supplier1->id,
                'ingredient_id' => $ingredient4->id,
                'quantity' => 10.00,
                'unit_price' => 30000.00,
                'purchase_date' => '2026-11-08',
                'notes' => 'Pembeli: Kafe Kopi Senja - Order rutin mingguan',
            ],
            [
                'supplier_id' => null,
                'ingredient_id' => $ingredient1->id,
                'quantity' => 4.00,
                'unit_price' => 35000.00,
                'purchase_date' => '2026-11-10',
                'notes' => 'Pembeli: Dimas Pratama - Pesanan via WhatsApp',
            ],
            [
                'supplier_id' => $supplier2->id,
                'ingredient_id' => $ingredient2->id,
                'quantity' => 12.00,
                'unit_price' => 24000.00,
                'purchase_date' => '2026-11-13',
                'notes' => 'Pembeli: Bu Sari - Pesanan acara keluarga',
            ],
            [
                'supplier_id' => null,
                'ingredient_id' => $ingredient3->id,
                'quantity' => 6.00,
                'unit_price' => 45000.00,
                'purchase_date' => '2026-11-15',
                'notes' => 'Pembeli: Warung Mbak Yuni - Repeat order',
            ],
            [
                'supplier_id' => $supplier1->id,
                'ingredient_id' => $ingredient4->id,
                'quantity' => 15.00,
                'unit_price' => 29000.00,
                'purchase_date' => '2026-11-18',
                'notes' => 'Pembeli: Kantor Kecamatan - Konsumsi rapat',
            ],
            [
                'supplier_id' => null,
                'ingredient_id' => $ingredient1->id,
                'quantity' => 7.00,
                'unit_price' => 35000.00,
                'purchase_date' => '2026-11-20',
                'notes' => 'Pembeli: Alya Putri - Order paket hemat',
            ],
            [
                'supplier_id' => $supplier2->id,
                'ingredient_id' => $ingredient2->id,
                'quantity' => 20.00,
                'unit_price' => 23500.00,
                'purchase_date' => '2026-11-23',
                'notes' => 'Pembeli: Catering Berkah - Pesanan partai besar',
            ],
        ];

        foreach ($purchases as $purchase) {
            $totalPrice = round($purchase['quantity'] * $purchase['unit_price'], 2);

            Purchase::firstOrCreate(
                [
                    'purchase_date' => $purchase['purchase_date'],
                    'notes' => $purchase['notes'],
                ],
                [
                    'supplier_id' => $purchase['supplier_id'],
                    'ingredient_id' => $purchase['ingredient_id'],
                    'quantity' => $purchase['quantity'],
                    'unit_price' => $purchase['unit_price'],
                    'total_price' => $totalPrice,
                ]
            );
        }
    }
}