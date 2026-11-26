
@extends('layouts.app')

@section('content')

<div class="container-fluid py-2">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h1 class="mb-0 fw-bold text-dark">Data Pembelian &amp; Pelanggan UMKM</h1>

            <small class="text-muted">
                @if(auth()->user()->isAdmin())
                    Monitoring seluruh transaksi pembelian UMKM.
                @else
                    Data transaksi dan pembelian usaha Anda.
                @endif
            </small>
        </div>

        @if(auth()->user()->isUmkm())
            <div class="d-flex gap-2">
                <button
                    type="button"
                    class="btn btn-success btn-sm px-3"
                    onclick="document.getElementById('buyer_or_notes').focus()">
                    + Transaksi Pembeli Baru
                </button>
            </div>
        @endif
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PERINGATAN STOK --}}
    @if($lowStockIngredients->count() > 0)
        <div class="alert alert-warning border-warning border-start border-4 mb-4 shadow-sm bg-white" role="alert">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-dark">
                    <strong>⚠️ Stok produk/bahan menipis:</strong>

                    @foreach($lowStockIngredients as $low)
                        <span class="badge bg-danger">
                            {{ $low->name }} (sisa {{ $low->stock }} {{ $low->unit }})
                        </span>
                    @endforeach
                </span>

                @if(auth()->user()->isUmkm())
                    <a
                        href="{{ route('pengeluaran.index', ['restock' => $lowStockIngredients->pluck('name')->implode(', ')]) }}"
                        class="btn btn-dark btn-sm">
                        Restock Cepat
                    </a>
                @endif
            </div>
        </div>
    @endif

    <div class="row g-3">

        {{-- FORM INPUT TRANSAKSI KHUSUS UMKM --}}
        @if(auth()->user()->isUmkm())
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm bg-white">

                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="card-title fw-bold m-0 text-dark">
                            Form Input Transaksi Pembeli
                        </h5>

                        <small class="text-muted">
                            Isi tanggal transaksi, supplier, informasi pembeli, dan item yang dibeli.
                        </small>
                    </div>

                    <div class="card-body">

                        <form action="{{ route('pembelian.store') }}" method="POST">
                            @csrf

                            {{-- TANGGAL DAN SUPPLIER --}}
                            <div class="row g-2 mb-3">

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">
                                        Tanggal Transaksi
                                    </label>

                                    <input
                                        type="date"
                                        name="purchase_date"
                                        value="{{ old('purchase_date', date('Y-m-d')) }}"
                                        class="form-control bg-light border-0"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-secondary mb-1">
                                        Sumber / Supplier
                                    </label>

                                    <select
                                        name="supplier_id"
                                        id="supplier_id"
                                        class="form-select bg-light border-0"
                                        required>

                                        <option value="">-- Pilih Supplier --</option>

                                        @foreach($suppliers as $sup)
                                            <option
                                                value="{{ $sup->id }}"
                                                {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                            </div>

                            {{-- CATATAN PEMBELI --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">
                                    Nama Pembeli / Catatan Pesanan
                                </label>

                                <input
                                    type="text"
                                    name="notes"
                                    id="buyer_or_notes"
                                    value="{{ old('notes') }}"
                                    class="form-control bg-light border-0"
                                    placeholder="Contoh: Pembeli: Bu Rina - Tunai / Transfer / Nota-090">
                            </div>

                            {{-- DAFTAR ITEM --}}
                            <div class="mb-3">

                                <label class="form-label small fw-semibold text-secondary mb-2 d-flex justify-content-between align-items-center">
                                    <span>Daftar Produk / Item yang Dibeli</span>

                                    <button
                                        type="button"
                                        class="btn btn-outline-dark btn-sm py-0 px-2"
                                        onclick="addFormRow()">
                                        + Tambah Item
                                    </button>
                                </label>

                                <div id="itemsContainer">

                                    <div class="row g-1 mb-2 align-items-center item-row">

                                        <div class="col-5">
                                            <select
                                                name="ingredient_ids[]"
                                                class="form-select form-select-sm ingredient-select bg-light border-0"
                                                required>

                                                <option value="">-- Pilih Item --</option>

                                                @foreach($ingredients as $ing)
                                                    <option
                                                        value="{{ $ing->id }}"
                                                        {{ old('ingredient_ids.0') == $ing->id ? 'selected' : '' }}>
                                                        {{ $ing->name }} ({{ $ing->unit }})
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>

                                        <div class="col-3">
                                            <input
                                                type="number"
                                                step="0.01"
                                                name="quantities[]"
                                                value="{{ old('quantities.0') }}"
                                                class="form-control form-control-sm qty-input bg-light border-0"
                                                placeholder="Qty"
                                                min="0.1"
                                                oninput="calculateTotal()"
                                                required>
                                        </div>

                                        <div class="col-4">
                                            <input
                                                type="number"
                                                name="unit_prices[]"
                                                value="{{ old('unit_prices.0') }}"
                                                class="form-control form-control-sm price-input bg-light border-0"
                                                placeholder="Harga Satuan"
                                                min="0"
                                                oninput="calculateTotal()"
                                                required>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            {{-- TOTAL --}}
                            <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-3">
                                <span class="fw-bold text-dark">Total Transaksi:</span>

                                <span class="fs-5 fw-bold text-success" id="totalLabel">
                                    Rp 0
                                </span>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-dark w-100 py-2 fw-semibold">
                                Simpan Transaksi Pembeli &amp; Update Stok
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- RIWAYAT TRANSAKSI --}}
        <div class="{{ auth()->user()->isAdmin() ? 'col-12' : 'col-lg-7' }}">

            <div class="card border-0 shadow-sm bg-white h-100">

                <div class="card-header bg-white py-3 border-bottom border-light">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <div>
                            <h5 class="card-title fw-bold m-0 text-dark">
                                Riwayat Pembelian &amp; Transaksi UMKM
                            </h5>

                            <small class="text-muted">
                                Daftar transaksi pembelian beserta detail item dan catatan pembeli.
                            </small>
                        </div>

                        {{-- FILTER --}}
                        <form
                            method="GET"
                            action="{{ url()->current() }}"
                            class="d-flex gap-1 flex-wrap"
                            id="filterForm">

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control form-control-sm bg-light border-0"
                                placeholder="Cari pembeli / item / supplier...">

                            <select
                                name="supplier_id"
                                class="form-select form-select-sm bg-light border-0"
                                onchange="document.getElementById('filterForm').submit()">

                                <option value="">Semua Supplier</option>

                                @foreach($suppliers as $sup)
                                    <option
                                        value="{{ $sup->id }}"
                                        {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->name }}
                                    </option>
                                @endforeach

                            </select>

                            <button type="submit" class="btn btn-dark btn-sm">
                                Cari
                            </button>

                            @if(request('search') || request('supplier_id'))
                                <a
                                    href="{{ url()->current() }}"
                                    class="btn btn-outline-secondary btn-sm">
                                    Reset
                                </a>
                            @endif

                        </form>

                    </div>
                </div>

                {{-- TABEL TRANSAKSI --}}
                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">

                        <thead class="table-light text-secondary">
                            <tr>
                                @if(auth()->user()->isAdmin())
                                    <th>Nama UMKM</th>
                                @endif

                                <th>Tanggal</th>
                                <th>Item &amp; Qty</th>
                                <th>Pembeli / Keterangan</th>
                                <th>Supplier</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($purchases as $p)
                                <tr>

                                    @if(auth()->user()->isAdmin())
                                        <td class="fw-semibold">
                                            {{ $p->umkm->name ?? 'UMKM tidak diketahui' }}
                                        </td>
                                    @endif

                                    <td class="text-nowrap text-muted">
                                        {{ $p->purchase_date }}
                                    </td>

                                    <td>
                                        <span class="fw-bold text-dark d-block">
                                            {{ $p->ingredient->name ?? '-' }}
                                        </span>

                                        <small class="text-muted">
                                            {{ $p->quantity }}
                                            {{ $p->ingredient->unit ?? '' }}
                                            @ Rp {{ number_format($p->unit_price, 0, ',', '.') }}
                                        </small>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal text-wrap text-start">
                                            {{ $p->notes ?? 'Pembeli Umum (Lunas)' }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        {{ $p->supplier->name ?? '-' }}
                                    </td>

                                    <td class="fw-bold text-success text-nowrap">
                                        Rp {{ number_format($p->total_price, 0, ',', '.') }}
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td
                                        colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}"
                                        class="text-center py-5 text-muted">

                                        Belum ada data transaksi pembelian yang tercatat.

                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                    {{-- PAGINATION --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">

                        <span class="small text-muted">
                            Showing {{ $purchases->firstItem() ?? 0 }}
                            to {{ $purchases->lastItem() ?? 0 }}
                            of {{ $purchases->total() ?? 0 }} results
                        </span>

                        <div>
                            {{ $purchases->withQueryString()->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- JAVASCRIPT KHUSUS UMKM --}}
@if(auth()->user()->isUmkm())
<script>

    const ingredientOptions = `
        <option value="">-- Pilih Item --</option>
        @foreach($ingredients as $ing)
            <option value="{{ $ing->id }}">
                {{ $ing->name }} ({{ $ing->unit }})
            </option>
        @endforeach
    `;

    function addFormRow(selectedId = '', defaultQty = '') {

        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');

        newRow.className = 'row g-1 mb-2 align-items-center item-row';

        newRow.innerHTML = `
            <div class="col-5">
                <select
                    name="ingredient_ids[]"
                    class="form-select form-select-sm ingredient-select bg-light border-0"
                    required>
                    ${ingredientOptions}
                </select>
            </div>

            <div class="col-3">
                <input
                    type="number"
                    step="0.01"
                    name="quantities[]"
                    value="${defaultQty}"
                    class="form-control form-control-sm qty-input bg-light border-0"
                    placeholder="Qty"
                    min="0.1"
                    oninput="calculateTotal()"
                    required>
            </div>

            <div class="col-4 d-flex gap-1">
                <input
                    type="number"
                    name="unit_prices[]"
                    class="form-control form-control-sm price-input bg-light border-0"
                    placeholder="Harga Satuan"
                    min="0"
                    oninput="calculateTotal()"
                    required>

                <button
                    type="button"
                    class="btn btn-danger btn-sm px-2"
                    onclick="this.closest('.item-row').remove(); calculateTotal();">
                    ×
                </button>
            </div>
        `;

        container.appendChild(newRow);

        if (selectedId) {
            newRow.querySelector('.ingredient-select').value = selectedId;
        }
    }

    function calculateTotal() {

        let total = 0;

        document.querySelectorAll('.item-row').forEach(row => {

            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;

            total += qty * price;

        });

        document.getElementById('totalLabel').innerText =
            'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', calculateTotal);

</script>
@endif

@endsection
