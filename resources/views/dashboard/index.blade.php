
@extends('layouts.app')

@section('title', 'Dashboard - UMKMHub')
@section('page-heading', 'Dashboard')

@section('content')

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Dashboard</h2>
        <p class="text-muted mb-0">
            Selamat datang di UMKMHub! Pantau perkembangan usaha Anda.
        </p>
    </div>

    <!-- STATISTIK -->
    <div class="row g-4 mb-4">

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Stok Bahan</p>
                            <h2 class="fw-bold mb-2" id="totalStok">0</h2>
                            <small class="text-muted">Jumlah seluruh stok</small>
                        </div>
                        <div class="dashboard-icon bg-primary-subtle text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Pembelian</p>
                            <h2 class="fw-bold mb-2" id="totalPembelian">0</h2>
                            <small class="text-muted">Jumlah transaksi</small>
                        </div>
                        <div class="dashboard-icon bg-success-subtle text-success">
                            <i class="bi bi-cart-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Total Pengeluaran</p>
                            <h2 class="fw-bold mb-2" id="totalPengeluaran">Rp0</h2>
                            <small class="text-muted">Seluruh transaksi</small>
                        </div>
                        <div class="dashboard-icon bg-danger-subtle text-danger">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-2">Jenis Bahan</p>
                            <h2 class="fw-bold mb-2" id="jenisBahan">0</h2>
                            <small class="text-muted">Bahan terdaftar</small>
                        </div>
                        <div class="dashboard-icon bg-warning-subtle text-warning">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- GRAFIK -->
    <div class="d-flex justify-content-end mb-3">
        <select id="filterPeriode" class="form-select w-auto">
            <option value="3">3 bulan terakhir</option>
            <option value="6" selected>6 bulan terakhir</option>
            <option value="12">12 bulan terakhir</option>
        </select>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Tren Pengeluaran</h5>
                    <small class="text-muted">Total pengeluaran per bulan</small>
                    <div class="mt-3" style="height:280px">
                        <canvas id="chartTren"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Per Kategori</h5>
                    <small class="text-muted">Porsi pengeluaran tiap kategori</small>
                    <div class="mt-3" style="height:280px">
                        <canvas id="chartKategori"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- INFORMASI TAMBAHAN -->
    <div class="row g-4">

        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">Pengeluaran Terbaru</h5>
                            <small class="text-muted">
                                Riwayat transaksi pengeluaran usaha
                            </small>
                        </div>

                        <a href="{{ url('/pengeluaran') }}"
                           class="btn btn-outline-primary btn-sm">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pengeluaran</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="aktivitasTable">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Memuat aktivitas...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-1">Informasi Stok</h5>
                    <small class="text-muted">Kondisi persediaan bahan</small>

                    <div class="mt-4">

                        <div class="d-flex justify-content-between mb-2">
                            <span>Stok Aman</span>
                            <strong id="stokAman">0</strong>
                        </div>

                        <div class="progress mb-4" style="height: 10px;">
                            <div class="progress-bar bg-success"
                                 id="progressAman" style="width: 0%">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Stok Menipis</span>
                            <strong id="stokMenipis">0</strong>
                        </div>

                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning"
                                 id="progressMenipis" style="width: 0%">
                            </div>
                        </div>

                    </div>

                    <a href="{{ url('/stok-bahan') }}"
                       class="btn btn-outline-primary w-100 mt-4">
                        Kelola Stok Bahan
                    </a>

                </div>
            </div>
        </div>

    </div>

    <div class="text-muted small mt-3" id="dashboardStatus">
        Memuat data dashboard...
    </div>

</div>

<style>
    .dashboard-card {
        min-height: 155px;
        border-radius: 12px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,.08) !important;
    }

    .dashboard-icon {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
        flex-shrink: 0;
    }

    #totalPengeluaran {
        font-size: clamp(1.35rem, 2.5vw, 2rem);
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('filterPeriode').addEventListener('change', renderGrafik);
    loadDashboard();
});

function formatRupiah(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(value) || 0);
}

function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

function formatTanggal(value) {
    if (!value) return '-';

    const date = new Date(String(value).substring(0, 10) + 'T00:00:00');

    if (isNaN(date.getTime())) return '-';

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

async function fetchAPI(url) {
    const response = await fetch(url, {
        headers: { 'Accept': 'application/json' }
    });

    // cek status dulu sebelum membaca JSON
    if (!response.ok) {
        throw new Error('Gagal mengambil data: ' + url + ' (HTTP ' + response.status + ')');
    }

    return await response.json();
}

async function loadDashboard() {
    const status = document.getElementById('dashboardStatus');

    try {
        // allSettled: kalau satu API gagal, data lain tetap tampil
        const hasil = await Promise.allSettled([
            fetchAPI('/api/v1/ingredients'),
            fetchAPI('/api/v1/purchases'),
            fetchAPI('/api/v1/expenses')
        ]);

        hasil.forEach(h => {
            if (h.status === 'rejected') console.error('Dashboard error:', h.reason);
        });

        const ambil = h => h.status === 'fulfilled' ? (h.value.data || []) : [];
        const ingredients = ambil(hasil[0]);
        const purchases   = ambil(hasil[1]);
        const expenses    = ambil(hasil[2]);
        const jumlahGagal = hasil.filter(h => h.status === 'rejected').length;

        // TOTAL STOK
        const totalStok = ingredients.reduce(
            (total, item) => total + Number(item.stock || 0), 0
        );

        document.getElementById('totalStok').textContent =
            totalStok.toLocaleString('id-ID');

        // TOTAL PEMBELIAN
        document.getElementById('totalPembelian').textContent =
            purchases.length;

        // TOTAL PENGELUARAN
        const totalPengeluaran = expenses.reduce(
            (total, item) => total + Number(item.amount || 0), 0
        );

        document.getElementById('totalPengeluaran').textContent =
            formatRupiah(totalPengeluaran);

        // JENIS BAHAN
        document.getElementById('jenisBahan').textContent =
            ingredients.length;

        // KONDISI STOK
        const stokMenipis = ingredients.filter(item =>
            Number(item.stock || 0) <= Number(item.min_stock || 0)
        ).length;

        const stokAman = ingredients.length - stokMenipis;
        const totalBahan = ingredients.length;

        document.getElementById('stokAman').textContent = stokAman;
        document.getElementById('stokMenipis').textContent = stokMenipis;

        document.getElementById('progressAman').style.width =
            (totalBahan ? stokAman / totalBahan * 100 : 0) + '%';

        document.getElementById('progressMenipis').style.width =
            (totalBahan ? stokMenipis / totalBahan * 100 : 0) + '%';

        // AKTIVITAS
        renderAktivitas(expenses);

        // GRAFIK
        semuaExpenses = expenses;
        renderGrafik();

        status.textContent = jumlahGagal
            ? 'Sebagian data gagal dimuat. Periksa API.'
            : 'Data dashboard berhasil diperbarui.';

    } catch (error) {
        console.error('Dashboard error:', error);
        status.textContent =
            'Gagal memuat sebagian data dashboard. Periksa API.';
    }
}

function renderAktivitas(expenses) {
    const table = document.getElementById('aktivitasTable');

    const terbaru = [...expenses]
        .sort((a, b) =>
            new Date(b.expense_date) - new Date(a.expense_date)
        )
        .slice(0, 5);

    if (terbaru.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    Belum ada aktivitas pengeluaran.
                </td>
            </tr>`;
        return;
    }

    table.innerHTML = terbaru.map(item => `
        <tr>
            <td>${formatTanggal(item.expense_date)}</td>
            <td class="fw-semibold">${escapeHTML(item.title)}</td>
            <td>
                <span class="badge bg-light text-dark">
                    ${escapeHTML(item.category)}
                </span>
            </td>
            <td class="text-danger fw-semibold">
                ${formatRupiah(item.amount)}
            </td>
        </tr>
    `).join('');
}

/* ===================== GRAFIK ===================== */

let semuaExpenses = [];
let chartTren, chartKategori;
const WARNA = ['#6b1a2b', '#3f9468', '#e0a043', '#5b8db8', '#a9a0a3', '#8e5ea2'];

function tglExpense(value) {
    return new Date(String(value).substring(0, 10) + 'T00:00:00');
}

function expensesPeriode() {
    const n = +document.getElementById('filterPeriode').value;
    const batas = new Date();
    batas.setMonth(batas.getMonth() - n);
    return semuaExpenses.filter(e => tglExpense(e.expense_date) >= batas);
}

function renderGrafik() {
    if (typeof Chart === 'undefined') return;

    const data = expensesPeriode();

    // total per bulan (kunci YYYY-MM)
    const perBulan = {};
    data.forEach(e => {
        const k = String(e.expense_date).substring(0, 7);
        perBulan[k] = (perBulan[k] || 0) + Number(e.amount || 0);
    });
    const bulan = Object.keys(perBulan).sort();
    const labelBulan = bulan.map(k =>
        new Date(k + '-01T00:00:00').toLocaleDateString('id-ID', { month: 'short', year: '2-digit' })
    );

    // total per kategori
    const perKat = {};
    data.forEach(e => {
        const k = e.category || 'Lainnya';
        perKat[k] = (perKat[k] || 0) + Number(e.amount || 0);
    });
    const kat = Object.keys(perKat);

    // grafik garis
    if (chartTren) chartTren.destroy();
    chartTren = new Chart(document.getElementById('chartTren'), {
        type: 'line',
        data: {
            labels: labelBulan,
            datasets: [{
                label: 'Pengeluaran',
                data: bulan.map(k => perBulan[k]),
                borderColor: '#6b1a2b',
                backgroundColor: 'rgba(107,26,43,.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => formatRupiah(c.parsed.y) } }
            },
            scales: { y: { ticks: { callback: v => formatRupiah(v) } } }
        }
    });

    // grafik donut
    if (chartKategori) chartKategori.destroy();
    chartKategori = new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',
        data: {
            labels: kat,
            datasets: [{
                data: kat.map(k => perKat[k]),
                backgroundColor: kat.map((_, i) => WARNA[i % WARNA.length])
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: c => c.label + ': ' + formatRupiah(c.parsed) } }
            }
        }
    });
}
</script>

@endsection

