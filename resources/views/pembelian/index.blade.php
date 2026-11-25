@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Pembelian</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('supplier_id').focus()">+ Transaksi Baru</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Smart Alert: Deteksi Stok Kritis dari Database Ingredients -->
    @if($lowStockIngredients->count() > 0)
    <div class="alert alert-warning border-warning border-start border-4 mb-4 shadow-sm" role="alert">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>
                <strong>⚠️ Stok kritis!</strong> Bahan berikut menipis:
                @foreach($lowStockIngredients as $low)
                    <span class="badge bg-danger">{{ $low->name }} (sisa {{ $low->stock }} {{ $low->unit }})</span>
                @endforeach
            </span>
            <button type="button" class="btn btn-dark btn-sm" onclick="autofillRestock()">[Restock Cepat]</button>
        </div>
    </div>
    @endif

    <div class="row g-3">
        <!-- FORM INPUT MULTI-ITEM & PEMASOK (Kiri) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title fw-bold m-0 text-dark">Form Input Multi-Item & Pemasok</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pembelian.store') }}" method="POST">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Tanggal Pembelian</label>
                                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted mb-1">Pemasok / Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-select" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Catatan / Keterangan (Opsional)</label>
                            <input type="text" name="notes" class="form-control" placeholder="Contoh: Tunai / Transfer / Nota-090">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted mb-2 d-flex justify-content-between align-items-center">
                                <span>Daftar Bahan yang Dibeli</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" onclick="addFormRow()">+ Tambah Bahan</button>
                            </label>

                            <div id="itemsContainer">
                                <div class="row g-1 mb-2 align-items-center item-row">
                                    <div class="col-5">
                                        <select name="ingredient_ids[]" class="form-select form-select-sm ingredient-select" required>
                                            <option value="">-- Pilih Bahan --</option>
                                            @foreach($ingredients as $ing)
                                                <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" step="0.01" name="quantities[]" class="form-control form-control-sm qty-input" placeholder="Qty" min="0.1" oninput="calculateTotal()" required>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" name="unit_prices[]" class="form-control form-control-sm price-input" placeholder="Harga Satuan" min="0" oninput="calculateTotal()" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3 mb-3">
                            <span class="fw-bold">Total Belanja:</span>
                            <span class="fs-5 fw-bold text-success" id="totalLabel">Rp 0</span>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2">Simpan Transaksi & Update Stok</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIWAYAT & PELACAKAN PEMBELIAN (Kanan) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title fw-bold m-0 text-dark">Riwayat & Pelacakan Pembelian</h5>
                        <form method="GET" action="{{ route('pembelian.index') }}" class="d-flex gap-1" id="filterForm">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari bahan / supplier...">
                            <select name="supplier_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                @endforeach
                            </select>
                            @if(request('search') || request('supplier_id'))
                                <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Bahan & Qty</th>
                                <th>Supplier</th>
                                <th>Total Harga</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $p)
                            <tr>
                                <td>{{ $p->purchase_date }}</td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $p->ingredient->name ?? '-' }}</span>
                                    <small class="text-muted">{{ $p->quantity }} {{ $p->ingredient->unit ?? '' }} @ Rp {{ number_format($p->unit_price, 0, ',', '.') }}</small>
                                </td>
                                <td>{{ $p->supplier->name ?? '-' }}</td>
                                <td class="fw-semibold text-success">Rp {{ number_format($p->total_price, 0, ',', '.') }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $p->notes ?? 'Selesai' }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat pembelian.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="small text-muted">Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() ?? 0 }} results</span>
                        <div>{{ $purchases->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ingredientOptions = `
        <option value="">-- Pilih Bahan --</option>
        @foreach($ingredients as $ing)
            <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
        @endforeach
    `;

    function addFormRow(selectedId = '', defaultQty = '') {
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row g-1 mb-2 align-items-center item-row';
        newRow.innerHTML = `
            <div class="col-5">
                <select name="ingredient_ids[]" class="form-select form-select-sm ingredient-select" required>
                    ${ingredientOptions}
                </select>
            </div>
            <div class="col-3">
                <input type="number" step="0.01" name="quantities[]" value="${defaultQty}" class="form-control form-control-sm qty-input" placeholder="Qty" min="0.1" oninput="calculateTotal()" required>
            </div>
            <div class="col-4 d-flex gap-1">
                <input type="number" name="unit_prices[]" class="form-control form-control-sm price-input" placeholder="Harga Satuan" min="0" oninput="calculateTotal()" required>
                <button type="button" class="btn btn-danger btn-sm px-2" onclick="this.closest('.item-row').remove(); calculateTotal();">×</button>
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
        document.getElementById('totalLabel').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function autofillRestock() {
        const lowItems = @json($lowStockIngredients->pluck('id'));
        if (lowItems.length === 0) return;

        const container = document.getElementById('itemsContainer');
        container.innerHTML = '';
        lowItems.forEach(id => addFormRow(id, 10));
        document.getElementById('supplier_id').focus();
    }
</script>

@endsection