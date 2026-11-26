
@extends('layouts.app')

@section('title', 'Dashboard Admin - UMKMHub')
@section('page-heading', 'Dashboard Admin')

@section('content')

<div class="container-fluid py-4">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Dashboard Admin</h2>
        <p class="text-muted mb-0">
            Selamat datang di panel Admin UMKMHub. Pantau pendaftaran dan aktivitas operasional seluruh UMKM.
        </p>
    </div>

    {{-- STATISTIK PENDAFTARAN --}}
    <h5 class="fw-bold mb-3">Statistik Pendaftaran UMKM</h5>

    <div class="row g-4 mb-4">

        @php
            $pendaftaran = [
                ['label' => 'Total UMKM', 'value' => $totalUmkm, 'icon' => 'bi-shop', 'class' => 'icon-total', 'desc' => 'Seluruh usaha terdaftar'],
                ['label' => 'Menunggu Persetujuan', 'value' => $totalPending, 'icon' => 'bi-clock-history', 'class' => 'icon-pending', 'desc' => 'Pendaftaran perlu ditinjau'],
                ['label' => 'Disetujui', 'value' => $totalApproved, 'icon' => 'bi-check-circle', 'class' => 'icon-approved', 'desc' => 'UMKM aktif terdaftar'],
                ['label' => 'Ditolak', 'value' => $totalRejected, 'icon' => 'bi-x-circle', 'class' => 'icon-rejected', 'desc' => 'Pendaftaran tidak disetujui'],
            ];
        @endphp

        @foreach ($pendaftaran as $item)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm admin-stat-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-2">{{ $item['label'] }}</p>
                                <h2 class="fw-bold mb-0">{{ $item['value'] }}</h2>
                            </div>
                            <div class="admin-icon {{ $item['class'] }}">
                                <i class="bi {{ $item['icon'] }}"></i>
                            </div>
                        </div>
                        <small class="text-muted">{{ $item['desc'] }}</small>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    {{-- STATISTIK OPERASIONAL --}}
    <h5 class="fw-bold mb-3">Monitoring Operasional UMKM</h5>

    <div class="row g-4 mb-4">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm admin-stat-card h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Jenis Bahan</p>
                    <h2 class="fw-bold mb-0">{{ $totalJenisBahan }}</h2>
                    <small class="text-muted">Seluruh bahan terdaftar</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm admin-stat-card h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Akumulasi Stok</p>
                    <h2 class="fw-bold mb-0">{{ number_format($totalStok, 0, ',', '.') }}</h2>
                    <small class="text-muted">Jumlah stok dari seluruh bahan, berbagai satuan</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm admin-stat-card h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Total Transaksi Pembelian</p>
                    <h2 class="fw-bold mb-0">{{ $totalPembelian }}</h2>
                    <small class="text-muted">Seluruh transaksi pembelian</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm admin-stat-card h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Total Pengeluaran</p>
                    <h2 class="fw-bold mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
                    <small class="text-muted">Akumulasi pengeluaran seluruh UMKM</small>
                </div>
            </div>
        </div>

    </div>

    {{-- PENDAFTARAN TERBARU --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Pendaftaran UMKM Terbaru</h5>
                <small class="text-muted">Lima UMKM terakhir yang mendaftar di UMKMHub</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Usaha</th>
                            <th>Pemilik</th>
                            <th>Jenis Usaha</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($umkmTerbaru as $umkm)
                            <tr>
                                <td class="fw-semibold">{{ $umkm->name }}</td>
                                <td>{{ $umkm->owner_name }}</td>
                                <td>{{ $umkm->business_type ?? '-' }}</td>
                                <td>{{ $umkm->created_at?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @if ($umkm->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning-emphasis">Menunggu</span>
                                    @elseif ($umkm->status === 'approved')
                                        <span class="badge bg-success-subtle text-success-emphasis">Disetujui</span>
                                    @elseif ($umkm->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger-emphasis">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak diketahui</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada UMKM yang mendaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PEMBELIAN TERBARU --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Aktivitas Pembelian Terbaru</h5>
                <small class="text-muted">Lima transaksi pembelian terakhir dari seluruh UMKM</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama UMKM</th>
                            <th>Bahan</th>
                            <th>Supplier</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembelianTerbaru as $pembelian)
                            <tr>
                                <td>{{ $pembelian->umkm->name ?? '-' }}</td>
                                <td>{{ $pembelian->ingredient->name ?? '-' }}</td>
                                <td>{{ $pembelian->supplier->name ?? '-' }}</td>
                                <td>{{ number_format($pembelian->quantity, 2, ',', '.') }}</td>
                                <td>Rp {{ number_format($pembelian->total_price, 0, ',', '.') }}</td>
                                <td>{{ $pembelian->purchase_date ? \Carbon\Carbon::parse($pembelian->purchase_date)->format('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada transaksi pembelian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PENGELUARAN TERBARU --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Aktivitas Pengeluaran Terbaru</h5>
                <small class="text-muted">Lima transaksi pengeluaran terakhir dari seluruh UMKM</small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama UMKM</th>
                            <th>Nama Pengeluaran</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengeluaranTerbaru as $pengeluaran)
                            <tr>
                                <td>{{ $pengeluaran->umkm->name ?? '-' }}</td>
                                <td>{{ $pengeluaran->title }}</td>
                                <td>{{ $pengeluaran->category }}</td>
                                <td>Rp {{ number_format($pengeluaran->amount, 0, ',', '.') }}</td>
                                <td>{{ $pengeluaran->expense_date ? \Carbon\Carbon::parse($pengeluaran->expense_date)->format('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada transaksi pengeluaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
    .admin-stat-card {
        border-radius: 14px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .admin-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,.08) !important;
    }

    .admin-icon {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        flex-shrink: 0;
    }

    .icon-total {
        background: #e8eee9;
        color: #3f6e49;
    }

    .icon-pending {
        background: #fff3d9;
        color: #b88628;
    }

    .icon-approved {
        background: #e5f2e8;
        color: #34834a;
    }

    .icon-rejected {
        background: #fbe9e7;
        color: #b54747;
    }
</style>

@endsection
