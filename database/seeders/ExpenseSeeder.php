<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $umkm = Umkm::where('name', 'Toko Roti Demo')->firstOrFail();

        $expenses = [
            [
                'title' => 'Pembelian Tepung Terigu',
                'category' => 'Bahan Baku',
                'amount' => 150000,
                'expense_date' => '2026-11-02',
                'description' => 'Pembelian 10 kg tepung terigu',
            ],
            [
                'title' => 'Pembelian Gula Pasir',
                'category' => 'Bahan Baku',
                'amount' => 85000,
                'expense_date' => '2026-11-04',
                'description' => 'Pembelian gula untuk produksi',
            ],
            [
                'title' => 'Pembayaran Listrik',
                'category' => 'Listrik dan Air',
                'amount' => 250000,
                'expense_date' => '2026-11-06',
                'description' => 'Tagihan listrik bulanan',
            ],
            [
                'title' => 'Biaya Pengiriman',
                'category' => 'Transportasi',
                'amount' => 75000,
                'expense_date' => '2026-11-08',
                'description' => 'Ongkos pengiriman produk',
            ],
            [
                'title' => 'Pembelian Minyak Goreng',
                'category' => 'Bahan Baku',
                'amount' => 120000,
                'expense_date' => '2026-11-10',
                'description' => 'Pembelian minyak goreng 5 liter',
            ],
            [
                'title' => 'Pembelian Kemasan Produk',
                'category' => 'Operasional',
                'amount' => 180000,
                'expense_date' => '2026-11-12',
                'description' => 'Pembelian plastik dan kotak kemasan',
            ],
            [
                'title' => 'Biaya Internet',
                'category' => 'Operasional',
                'amount' => 150000,
                'expense_date' => '2026-11-15',
                'description' => 'Pembayaran internet usaha',
            ],
            [
                'title' => 'Pembelian Telur',
                'category' => 'Bahan Baku',
                'amount' => 95000,
                'expense_date' => '2026-11-18',
                'description' => 'Pembelian telur untuk produksi',
            ],
            [
                'title' => 'Perawatan Peralatan',
                'category' => 'Operasional',
                'amount' => 200000,
                'expense_date' => '2026-11-20',
                'description' => 'Perawatan alat produksi',
            ],
            [
                'title' => 'Pembelian Gas LPG',
                'category' => 'Operasional',
                'amount' => 220000,
                'expense_date' => '2026-11-23',
                'description' => 'Pembelian gas untuk produksi',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::updateOrCreate(
                [
                    'umkm_id' => $umkm->id,
                    'title' => $expense['title'],
                    'expense_date' => $expense['expense_date'],
                ],
                $expense
            );
        }
    }
}