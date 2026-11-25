@extends('layouts.app')

@section('title', 'Analisis Data - UMKMHub')
@section('page-heading', 'Analisis Data')

@section('styles')
<style>
    .analysis-metric-value {
        font-size: clamp(1.1rem, 1.55vw, 1.5rem);
        line-height: 1.25;
        white-space: nowrap;
        letter-spacing: -0.01em;
    }

    .analysis-filter-card .card-body {
        padding: 12px 16px;
    }

    .analysis-chart-box {
        position: relative;
        height: 310px;
        width: 100%;
    }
</style>
@endsection

@section('content')

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">Analisis Data</h2>
            <p class="text-muted mb-0">
                <i>Visualisasi Tren Toko Juragan</i>.
            </p>
        </div>

        <div class="card border-0 shadow-sm analysis-filter-card">
            <div class="card-body d-flex flex-wrap align-items-center gap-2">
                <select id="filterPeriod" class="form-select form-select-sm w-auto">
                    <option value="7d">7 Hari Terakhir</option>
                    <option value="30d" selected>30 Hari Terakhir</option>
                    <option value="90d">90 Hari Terakhir</option>
                    <option value="all">Semua Data</option>
                </select>

                <input id="filterStartDate" type="date" class="form-control form-control-sm w-auto">
                <span class="text-muted small px-1">s/d</span>
                <input id="filterEndDate" type="date" class="form-control form-control-sm w-auto">

                <button id="btnApplyFilter" type="button" class="btn btn-primary btn-sm px-3">
                    Terapkan
                </button>
                <button id="btnResetFilter" type="button" class="btn btn-outline-secondary btn-sm px-3">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted small mb-2">Total Pembelian (Pemasukan)</p>
                    <div class="fw-bold analysis-metric-value mb-2" id="analisisTotalPembelian">Rp0</div>
                    <small class="text-muted d-block" id="analisisJumlahPembelian">0 transaksi pembelian</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted small mb-2">Total Pengeluaran (Periode)</p>
                    <div class="fw-bold analysis-metric-value mb-2" id="analisisTotalPengeluaran">Rp0</div>
                    <small class="text-muted d-block" id="analisisJumlahTransaksi">0 transaksi tercatat</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted small mb-2">Keuntungan Bersih (Periode)</p>
                    <div class="fw-bold analysis-metric-value mb-2" id="analisisKeuntunganBersih">Rp0</div>
                    <small class="text-muted d-block" id="analisisStatusUntung">Pembelian - Pengeluaran</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted small mb-2">Kategori / Bahan Terpantau</p>
                    <div class="fw-bold analysis-metric-value mb-2" id="analisisJumlahKategori">0</div>
                    <small class="text-muted d-block" id="analisisKategoriTerbesar">Tertinggi: -</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Grafik Garis: Tren Pengeluaran &amp; Pembelian</h5>
                    <p class="text-muted small mb-3">Pergerakan nominal pengeluaran dan pembelian berdasarkan tanggal</p>
                    <div class="analysis-chart-box">
                        <canvas id="chartExpenseLine"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Grafik Keuntungan Harian</h5>
                    <p class="text-muted small mb-3">Selisih pembelian (pemasukan) dikurangi pengeluaran per tanggal</p>
                    <div class="analysis-chart-box">
                        <canvas id="chartProfit"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Grafik Batang: Pengeluaran per Kategori</h5>
                    <p class="text-muted small mb-3">Perbandingan total biaya antar kategori pengeluaran</p>
                    <div class="analysis-chart-box">
                        <canvas id="chartCategoryBar"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-1">Pie / Donut Chart: Proporsi Kategori</h5>
                    <p class="text-muted small mb-3">Persentase porsi biaya tiap kategori pada periode terpilih</p>
                    <div class="analysis-chart-box">
                        <canvas id="chartCategoryPie"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Ringkasan Analisis</h5>
            <ul id="analysisSummary" class="list-unstyled mb-0 text-muted"></ul>
        </div>
    </div>

    <div class="text-muted small mt-3 mb-2" id="analysisStatus">
        Memuat data analisis...
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const state = {
        period: '30d',
        startDate: '',
        endDate: '',
        charts: {},
        ingredients: [],
        purchases: [],
        expenses: []
    };

    const periodSelect = document.getElementById('filterPeriod');
    const startDateInput = document.getElementById('filterStartDate');
    const endDateInput = document.getElementById('filterEndDate');
    const applyBtn = document.getElementById('btnApplyFilter');
    const resetBtn = document.getElementById('btnResetFilter');

    periodSelect?.addEventListener('change', (e) => {
        state.period = e.target.value;
        state.startDate = '';
        state.endDate = '';
        if (startDateInput) startDateInput.value = '';
        if (endDateInput) endDateInput.value = '';
        renderAnalysis();
    });

    applyBtn?.addEventListener('click', () => {
        state.startDate = startDateInput?.value || '';
        state.endDate = endDateInput?.value || '';
        renderAnalysis();
    });

    resetBtn?.addEventListener('click', () => {
        state.period = '30d';
        state.startDate = '';
        state.endDate = '';
        if (periodSelect) periodSelect.value = '30d';
        if (startDateInput) startDateInput.value = '';
        if (endDateInput) endDateInput.value = '';
        renderAnalysis();
    });

    initAnalysis();

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(Number(value) || 0);
    }

    function formatCompactRupiah(value) {
        const n = Number(value) || 0;
        const sign = n < 0 ? '-' : '';
        const abs = Math.abs(n);
        if (abs >= 1_000_000) return `${sign}Rp ${(abs / 1_000_000).toFixed(1)}jt`;
        if (abs >= 1_000) return `${sign}Rp ${(abs / 1_000).toFixed(0)}rb`;
        return `${sign}Rp ${abs}`;
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

    async function fetchAPI(url) {
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });
        const result = await response.json();
        if (!response.ok) {
            throw new Error('Gagal memuat ' + url);
        }
        return result;
    }

    async function initAnalysis() {
        const statusEl = document.getElementById('analysisStatus');
        try {
            const [ingRes, purRes, expRes] = await Promise.all([
                fetchAPI('/api/v1/ingredients'),
                fetchAPI('/api/v1/purchases'),
                fetchAPI('/api/v1/expenses')
            ]);

            state.ingredients = Array.isArray(ingRes.data) ? ingRes.data : [];
            state.purchases = Array.isArray(purRes.data) ? purRes.data : [];
            state.expenses = Array.isArray(expRes.data) ? expRes.data : [];

            renderAnalysis();
            if (statusEl) statusEl.textContent = 'Data analisis berhasil diperbarui.';
        } catch (err) {
            console.error(err);
            if (statusEl) statusEl.textContent = 'Gagal memuat data dari API. Periksa endpoint /api/v1.';
        }
    }

    function parseDateOnly(raw) {
        if (!raw) return null;
        const d = new Date(String(raw).substring(0, 10) + 'T00:00:00');
        return isNaN(d.getTime()) ? null : d;
    }

    function isWithinFilter(rawDate) {
        const date = parseDateOnly(rawDate);
        if (!date) return state.period === 'all' && !state.startDate && !state.endDate;

        if (state.startDate) {
            const start = parseDateOnly(state.startDate);
            if (start && date < start) return false;
        }
        if (state.endDate) {
            const end = parseDateOnly(state.endDate);
            if (end && date > end) return false;
        }
        if (state.startDate || state.endDate || state.period === 'all') {
            return true;
        }

        const days = state.period === '7d' ? 7 : state.period === '90d' ? 90 : 30;
        const cutoff = new Date();
        cutoff.setHours(0, 0, 0, 0);
        cutoff.setDate(cutoff.getDate() - days);
        return date >= cutoff;
    }

    function buildDailyMap(expenses, purchases) {
        const map = new Map();
        expenses.forEach(item => {
            const key = String(item.expense_date ?? item.date ?? item.created_at ?? '').substring(0, 10) || 'Tanpa Tanggal';
            const row = map.get(key) || { exp: 0, pur: 0 };
            row.exp += Number(item.amount ?? item.total ?? 0);
            map.set(key, row);
        });
        purchases.forEach(item => {
            const key = String(item.purchase_date ?? item.date ?? item.created_at ?? '').substring(0, 10) || 'Tanpa Tanggal';
            const row = map.get(key) || { exp: 0, pur: 0 };
            row.pur += Number(item.total_price ?? item.total ?? item.amount ?? 0);
            map.set(key, row);
        });
        return map;
    }

    function renderAnalysis() {
        const filteredExpenses = state.expenses.filter(item =>
            isWithinFilter(item.expense_date ?? item.date ?? item.created_at)
        );
        const filteredPurchases = state.purchases.filter(item =>
            isWithinFilter(item.purchase_date ?? item.date ?? item.created_at)
        );

        const totalExp = filteredExpenses.reduce((sum, i) => sum + Number(i.amount ?? i.total ?? 0), 0);
        const totalPur = filteredPurchases.reduce((sum, i) => sum + Number(i.total_price ?? i.total ?? i.amount ?? 0), 0);
        const netProfit = totalPur - totalExp;

        const categoryMap = new Map();
        filteredExpenses.forEach(item => {
            const cat = item.category || 'Lainnya';
            const amt = Number(item.amount ?? item.total ?? 0);
            const curr = categoryMap.get(cat) || { total: 0, count: 0 };
            curr.total += amt;
            curr.count += 1;
            categoryMap.set(cat, curr);
        });

        const sortedCategories = [...categoryMap.entries()].sort((a, b) => b[1].total - a[1].total);
        const topCategory = sortedCategories[0];
        const dailyMap = buildDailyMap(filteredExpenses, filteredPurchases);

        document.getElementById('analisisTotalPengeluaran').textContent = formatRupiah(totalExp);
        document.getElementById('analisisJumlahTransaksi').textContent = `${filteredExpenses.length} transaksi tercatat`;
        document.getElementById('analisisTotalPembelian').textContent = formatRupiah(totalPur);
        document.getElementById('analisisJumlahPembelian').textContent = `${filteredPurchases.length} transaksi pembelian`;

        const profitEl = document.getElementById('analisisKeuntunganBersih');
        const statusUntungEl = document.getElementById('analisisStatusUntung');
        profitEl.textContent = formatRupiah(netProfit);
        profitEl.classList.toggle('text-success', netProfit >= 0);
        profitEl.classList.toggle('text-danger', netProfit < 0);
        if (statusUntungEl) {
            statusUntungEl.textContent = netProfit >= 0 ? 'Surplus / Untung bersih' : 'Defisit / Pengeluaran melebihi pembelian';
        }

        document.getElementById('analisisJumlahKategori').textContent = sortedCategories.length;
        document.getElementById('analisisKategoriTerbesar').textContent = topCategory
            ? `Tertinggi: ${topCategory[0]} (${formatCompactRupiah(topCategory[1].total)})`
            : 'Tertinggi: -';

        renderLineChart(dailyMap);
        renderProfitChart(dailyMap);
        renderCategoryCharts(sortedCategories);
        renderSummary(totalExp, totalPur, netProfit, filteredExpenses.length, topCategory);
    }

    function destroyChart(key) {
        if (state.charts[key]) {
            state.charts[key].destroy();
            state.charts[key] = null;
        }
    }

    function renderLineChart(dailyMap) {
        const ctx = document.getElementById('chartExpenseLine')?.getContext('2d');
        if (!ctx || typeof Chart === 'undefined') return;
        destroyChart('line');

        const labels = [...dailyMap.keys()].sort();
        state.charts.line = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Pengeluaran (Rp)',
                        data: labels.map(k => dailyMap.get(k).exp),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.12)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Pembelian (Rp)',
                        data: labels.map(k => dailyMap.get(k).pur),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderDash: [5, 5],
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => formatCompactRupiah(v) }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: c => `${c.dataset.label}: ${formatRupiah(c.parsed.y)}`
                        }
                    }
                }
            }
        });
    }

    function renderProfitChart(dailyMap) {
        const ctx = document.getElementById('chartProfit')?.getContext('2d');
        if (!ctx || typeof Chart === 'undefined') return;
        destroyChart('profit');

        const labels = [...dailyMap.keys()].sort();
        const profits = labels.map(k => {
            const row = dailyMap.get(k);
            return row.pur - row.exp;
        });

        state.charts.profit = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Keuntungan Harian (Rp)',
                    data: profits,
                    backgroundColor: profits.map(v => v >= 0 ? 'rgba(25, 135, 84, 0.8)' : 'rgba(220, 53, 69, 0.8)'),
                    borderColor: profits.map(v => v >= 0 ? '#198754' : '#dc3545'),
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: c => {
                                const val = Number(c.parsed.y) || 0;
                                const status = val >= 0 ? 'Untung' : 'Rugi';
                                return `${status}: ${formatRupiah(val)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: { callback: v => formatCompactRupiah(v) }
                    }
                }
            }
        });
    }

    function renderCategoryCharts(sortedCategories) {
        const barCtx = document.getElementById('chartCategoryBar')?.getContext('2d');
        const pieCtx = document.getElementById('chartCategoryPie')?.getContext('2d');
        if (typeof Chart === 'undefined') return;

        destroyChart('bar');
        destroyChart('pie');

        const labels = sortedCategories.map(([k]) => k);
        const values = sortedCategories.map(([, v]) => v.total);
        const palette = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997'];

        if (barCtx) {
            state.charts.bar = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Pengeluaran (Rp)',
                        data: values,
                        backgroundColor: palette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: { label: c => formatRupiah(c.parsed.y) }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => formatCompactRupiah(v) }
                        }
                    }
                }
            });
        }

        if (pieCtx) {
            state.charts.pie = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: palette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: c => {
                                    const sum = values.reduce((a, b) => a + b, 0) || 1;
                                    const pct = ((c.parsed / sum) * 100).toFixed(1);
                                    return `${c.label}: ${formatRupiah(c.parsed)} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    function renderSummary(totalExp, totalPur, netProfit, trxCount, topCategory) {
        const el = document.getElementById('analysisSummary');
        if (!el) return;

        const lines = [
            `Pada rentang data terpilih, tercatat ${trxCount} transaksi pengeluaran dengan total pengeluaran ${formatRupiah(totalExp)}, total pembelian ${formatRupiah(totalPur)}, dan keuntungan bersih ${formatRupiah(netProfit)}.`,
            topCategory
                ? `Kategori dengan pengeluaran tertinggi adalah "${topCategory[0]}" sebesar ${formatRupiah(topCategory[1].total)} (${topCategory[1].count} transaksi).`
                : 'Belum ada data kategori pengeluaran pada periode yang dipilih.',
            'Gunakan grafik keuntungan harian dan grafik garis untuk memantau hari dengan selisih positif maupun minus.'
        ];

        el.innerHTML = lines.map(item => `<li class="mb-2">• ${escapeHTML(item)}</li>`).join('');
    }
});
</script>
@endsection