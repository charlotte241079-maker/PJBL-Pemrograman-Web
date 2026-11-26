<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    public function run(): void
    {
        $umkm = Umkm::where('name', 'Toko Roti Demo')->firstOrFail();

        $items = [
            [
                'title' => 'Pembelian Biji Kopi Arabika 5kg',
                'category' => 'Bahan Baku',
                'amount' => 650000,
                'expense_date' => '2026-11-14',
                'description' => 'Stok kopi mingguan',
            ],
            [
                'title' => 'Gula Aren Cair 10 Liter',
                'category' => 'Bahan Baku',
                'amount' => 280000,
                'expense_date' => '2026-11-15',
                'description' => 'Supplier langganan',
            ],
            [
                'title' => 'Susu UHT Full Cream 2 Dus',
                'category' => 'Bahan Baku',
                'amount' => 420000,
                'expense_date' => '2026-11-16',
                'description' => 'Stok minuman',
            ],
            [
                'title' => 'Cup Plastik & Tutup (500 pcs)',
                'category' => 'Kemasan',
                'amount' => 225000,
                'expense_date' => '2026-11-17',
                'description' => 'Kemasan take away',
            ],
            [
                'title' => 'Token Listrik Kedai',
                'category' => 'Operasional',
                'amount' => 500000,
                'expense_date' => '2026-11-18',
                'description' => 'Listrik bulanan',
            ],
            [
                'title' => 'Gas LPG 12kg (2 Tabung)',
                'category' => 'Operasional',
                'amount' => 440000,
                'expense_date' => '2026-11-19',
                'description' => 'Kebutuhan dapur',
            ],
            [
                'title' => 'Ayam Fillet Dada 10kg',
                'category' => 'Bahan Baku',
                'amount' => 480000,
                'expense_date' => '2026-11-20',
                'description' => 'Menu ayam geprek',
            ],
            [
                'title' => 'Minyak Goreng & Bumbu Dapur',
                'category' => 'Bahan Baku',
                'amount' => 310000,
                'expense_date' => '2026-11-21',
                'description' => 'Belanja pasar pagi',
            ],
            [
                'title' => 'Paper Bowl & Sendok Plastik',
                'category' => 'Kemasan',
                'amount' => 195000,
                'expense_date' => '2026-11-22',
                'description' => 'Kemasan makanan',
            ],
            [
                'title' => 'Biaya Iklan Instagram Ads',
                'category' => 'Pemasaran',
                'amount' => 150000,
                'expense_date' => '2026-11-23',
                'description' => 'Promo akhir pekan',
            ],
            [
                'title' => 'Air Galon & Es Batu Kristal',
                'category' => 'Operasional',
                'amount' => 120000,
                'expense_date' => '2026-11-24',
                'description' => 'Kebutuhan harian',
            ],
            [
                'title' => 'Bubuk Matcha & Cokelat Premium',
                'category' => 'Bahan Baku',
                'amount' => 340000,
                'expense_date' => '2026-11-25',
                'description' => 'Varian minuman non-kopi',
            ],
        ];

        foreach ($items as $item) {
            Expense::updateOrCreate(
                [
                    'umkm_id' => $umkm->id,
                    'title' => $item['title'],
                    'expense_date' => $item['expense_date'],
                ],
                $item
            );
        }
    }
}