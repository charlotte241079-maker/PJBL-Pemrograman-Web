<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $baseCurrency = strtoupper($request->query('from', 'IDR'));
        $amount       = (float) $request->query('amount', Expense::sum('amount'));

        $response = Http::timeout(10)->get("https://open.er-api.com/v6/latest/{$baseCurrency}");

        if ($response->failed() || ($response->json('result') !== 'success')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data kurs mata uang dari API eksternal',
            ], 502);
        }

        $data  = $response->json();
        $rates = $data['rates'] ?? [];

        $convertedAll = [];
        foreach ($rates as $currencyCode => $rate) {
            $convertedAll[$currencyCode] = round($amount * $rate, 4);
        }

        return response()->json([
            'status'           => 'success',
            'source_api'       => 'ExchangeRate-API (All World Currencies)',
            'last_update'      => $data['time_last_update_utc'] ?? now()->toDateTimeString(),
            'base_currency'    => $baseCurrency,
            'total_currencies' => count($rates),
            'nominal_awal'     => $amount,
            'kurs_per_1_unit'  => $rates,
            'hasil_konversi'   => $convertedAll,
        ]);
    }
}