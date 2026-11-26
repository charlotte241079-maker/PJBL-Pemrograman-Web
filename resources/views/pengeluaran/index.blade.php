
@extends('layouts.app')

@section('title', 'Pengeluaran - UMKMHub')
@section('page-heading', 'Pengeluaran')

@section('content')

@php
    $isAdmin = auth()->user()->isAdmin();
@endphp

<div class="container-fluid py-3">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">Pengeluaran</h2>
            <p class="text-muted mb-0">
                {{ $isAdmin
                    ? 'Pemantauan pengeluaran seluruh UMKM'
                    : 'Pemantauan Keuangan Usaha' }}
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">

            <div class="input-group" style="width: auto;">
                <span class="input-group-text bg-white text-muted small">
                    <i class="bi bi-currency-exchange me-1"></i> Kurs
                </span>
                <select id="currencySelector"
                    class="form-select fw-semibold"
                    style="min-width: 110px;">
                    <option value="IDR">IDR</option>
                </select>
            </div>

            @unless($isAdmin)
                <button class="btn btn-primary" onclick="openExpenseForm()">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Pengeluaran
                </button>
            @endunless

        </div>
    </div>

    <!-- RINGKASAN -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Total Transaksi</p>
                            <h3 class="fw-bold mb-0" id="totalTransaksi">0</h3>
                        </div>
                        <div class="text-primary fs-2">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Total Pengeluaran</p>
                            <h3 class="fw-bold mb-0" id="totalPengeluaran">Rp0</h3>
                            <small class="text-muted" id="kursInfoText"></small>
                        </div>
                        <div class="text-danger fs-2">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Pengeluaran Bulan Ini</p>
                            <h3 class="fw-bold mb-0" id="pengeluaranBulanIni">Rp0</h3>
                        </div>
                        <div class="text-success fs-2">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- TABEL -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">Riwayat Pengeluaran</h5>
                    <p class="text-muted small mb-0">
                        {{ $isAdmin
                            ? 'Daftar pengeluaran dari seluruh UMKM yang terdaftar.'
                            : 'Daftar seluruh transaksi pengeluaran usaha.' }}
                    </p>
                </div>

                <div style="max-width: 280px; width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search"
                            id="searchExpense"
                            class="form-control"
                            placeholder="Cari pengeluaran...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>

                            @if($isAdmin)
                                <th>Nama UMKM</th>
                            @endif

                            <th>Nama Pengeluaran</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>

                            @unless($isAdmin)
                                <th>Aksi</th>
                            @endunless
                        </tr>
                    </thead>

                    <tbody id="expenseTable">
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 7 }}"
                                class="text-center py-4">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>


{{-- MODAL HANYA UNTUK UMKM --}}
@unless($isAdmin)

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <form id="expenseForm">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitle">
                        Tambah Pengeluaran
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="expenseId">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Nama Pengeluaran
                        </label>
                        <input type="text"
                            id="title"
                            class="form-control"
                            placeholder="Contoh: Pembelian tepung"
                            maxlength="255"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kategori</label>
                        <select id="category" class="form-select" required>
                            <option value="">Pilih kategori</option>
                            <option value="Bahan Baku">Bahan Baku</option>
                            <option value="Operasional">Operasional</option>
                            <option value="Transportasi">Transportasi</option>
                            <option value="Listrik dan Air">Listrik dan Air</option>
                            <option value="Sewa Tempat">Sewa Tempat</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">
                                Volume / Qty (Opsional)
                            </label>
                            <input type="number"
                                id="qty"
                                class="form-control"
                                placeholder="Contoh: 10"
                                min="0.01"
                                step="0.01">
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold">Satuan</label>
                            <select id="unit" class="form-select">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="kg">kg</option>
                                <option value="pcs">pcs</option>
                                <option value="liter">liter</option>
                                <option value="gram">gram</option>
                                <option value="ml">ml</option>
                                <option value="box">box</option>
                                <option value="pack">pack</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Jumlah Pengeluaran (Rp)
                        </label>
                        <input type="number"
                            id="amount"
                            class="form-control"
                            placeholder="Masukkan nominal"
                            min="0.01"
                            step="0.01"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Tanggal Pengeluaran
                        </label>
                        <input type="date"
                            id="expense_date"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea id="description"
                            class="form-control"
                            rows="3"
                            placeholder="Catatan tambahan"></textarea>
                    </div>

                    <div id="formError" class="text-danger small"></div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                        class="btn btn-primary"
                        id="btnSimpan">
                        <i class="bi bi-save me-1"></i>
                        Simpan Pengeluaran
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endunless


<script>
const isAdmin = @json($isAdmin);

let expenses = [];
let lastSummary = {};
let currencyRates = { IDR: 1 };
let selectedCurrency = 'IDR';
let expenseModal = null;

document.addEventListener('DOMContentLoaded', function () {

    @unless($isAdmin)
        expenseModal = new bootstrap.Modal(
            document.getElementById('expenseModal')
        );

        document.getElementById('expenseForm')
            .addEventListener('submit', saveExpense);
    @endunless

    loadCurrencyRates();
    loadExpenses();

    document.getElementById('searchExpense')
        .addEventListener('input', renderExpenses);

    document.getElementById('currencySelector')
        .addEventListener('change', function () {
            selectedCurrency = this.value;
            updateSummary(lastSummary);
            renderExpenses();
        });

    @unless($isAdmin)
        const params = new URLSearchParams(window.location.search);
        const restockItems = (params.get('restock') || '').trim();

        if (restockItems) {
            openExpenseForm({
                title: `Restock Bahan: ${restockItems}`,
                category: 'Bahan Baku',
                qty: 10,
                unit: 'kg',
                description: `Restock cepat untuk bahan stok menipis: ${restockItems}`
            });

            window.history.replaceState(
                {},
                document.title,
                window.location.pathname
            );
        }
    @endunless

});


// MEMBACA CSRF TOKEN UNTUK REQUEST API
function getCsrfToken() {
    const cookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='));

    return cookie
        ? decodeURIComponent(cookie.split('=').slice(1).join('='))
        : '';
}


// MEMUAT KURS MATA UANG
async function loadCurrencyRates() {
    const selector = document.getElementById('currencySelector');
    const infoText = document.getElementById('kursInfoText');

    try {
        const response = await fetch('/api/v1/currency', {
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();

        if (response.ok && result.status === 'success'
            && result.kurs_per_1_unit) {

            currencyRates = result.kurs_per_1_unit;

            const codes = Object.keys(currencyRates);

            selector.innerHTML = codes.map(code =>
                `<option value="${escapeHTML(code)}"
                    ${code === 'IDR' ? 'selected' : ''}>
                    ${escapeHTML(code)}
                </option>`
            ).join('');

            if (infoText) {
                infoText.textContent =
                    `${result.total_currencies} mata uang tersedia`;
            }
        }
    } catch (error) {
        console.error('Gagal memuat kurs:', error);
    }
}


// FORMAT NOMINAL
function formatMoney(valueInIdr) {
    const amount = Number(valueInIdr) || 0;

    if (selectedCurrency === 'IDR') {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(amount);
    }

    const rate = Number(currencyRates[selectedCurrency]) || 1;
    const converted = amount * rate;

    return `${selectedCurrency} ` +
        new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 4
        }).format(converted);
}


// FORMAT TANGGAL
function formatTanggal(value) {
    if (!value) return '-';

    const date = new Date(String(value).substring(0, 10) + 'T00:00:00');

    if (isNaN(date.getTime())) return escapeHTML(value);

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}


// MENCEGAH HTML INJECTION
function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}


// MEMUAT DATA PENGELUARAN DARI API
async function loadExpenses() {
    const tbody = document.getElementById('expenseTable');
    const colspan = isAdmin ? 7 : 7;

    try {
        const response = await fetch('/api/v1/expenses', {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil data.');
        }

        expenses = result.data || [];
        lastSummary = result.summary || {};

        updateSummary(lastSummary);
        renderExpenses();

    } catch (error) {
        console.error(error);

        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}"
                    class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Gagal memuat data pengeluaran.
                    <br>
                    <small>${escapeHTML(error.message)}</small>
                </td>
            </tr>
        `;
    }
}


// MEMPERBARUI RINGKASAN
function updateSummary(summary) {
    document.getElementById('totalTransaksi').textContent =
        summary.total_transaksi ?? expenses.length;

    document.getElementById('totalPengeluaran').textContent =
        formatMoney(summary.total_pengeluaran);

    const now = new Date();

    const totalBulanIni = expenses.reduce((total, item) => {
        const date = String(item.expense_date || '').substring(0, 10);
        const [year, month] = date.split('-').map(Number);

        if (
            year === now.getFullYear() &&
            month === now.getMonth() + 1
        ) {
            return total + Number(item.amount || 0);
        }

        return total;
    }, 0);

    document.getElementById('pengeluaranBulanIni').textContent =
        formatMoney(totalBulanIni);
}


// MENAMPILKAN TABEL
function renderExpenses() {
    const keyword = document.getElementById('searchExpense')
        .value.toLowerCase().trim();

    const filtered = expenses.filter(item => {
        const umkmName = item.umkm?.name || '';

        return (
            (item.title || '').toLowerCase().includes(keyword) ||
            (item.category || '').toLowerCase().includes(keyword) ||
            (item.description || '').toLowerCase().includes(keyword) ||
            umkmName.toLowerCase().includes(keyword)
        );
    });

    const tbody = document.getElementById('expenseTable');
    const colspan = isAdmin ? 7 : 7;

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}"
                    class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    Belum ada data pengeluaran.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filtered.map((item, index) => {

        const automatic = item.purchase_id !== null
            && item.purchase_id !== undefined;

        let actionColumn = '';

        if (!isAdmin) {
            if (automatic) {
                actionColumn = `
                    <td>
                        <span class="badge bg-info-subtle text-info">
                            Otomatis dari Pembelian
                        </span>
                    </td>
                `;
            } else {
                actionColumn = `
                    <td>
                        <div class="d-flex gap-1">
                            <button
                                class="btn btn-sm btn-outline-primary"
                                onclick="editExpense(${Number(item.id)})"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <button
                                class="btn btn-sm btn-outline-danger"
                                onclick="deleteExpense(${Number(item.id)})"
                                title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
            }
        }

        const umkmColumn = isAdmin ? `
            <td class="fw-semibold">
                ${escapeHTML(item.umkm?.name || 'UMKM tidak diketahui')}
            </td>
        ` : '';

        return `
            <tr>
                <td>${index + 1}</td>

                <td>${formatTanggal(item.expense_date)}</td>

                ${umkmColumn}

                <td class="fw-semibold">
                    ${escapeHTML(item.title)}
                </td>

                <td>
                    <span class="badge bg-light text-dark border">
                        ${escapeHTML(item.category)}
                    </span>
                </td>

                <td class="fw-semibold text-danger">
                    ${formatMoney(item.amount)}
                </td>

                <td>
                    ${escapeHTML(item.description || '-')}
                </td>

                ${actionColumn}
            </tr>
        `;
    }).join('');
}


@unless($isAdmin)

// MENGGABUNGKAN QTY, SATUAN, DAN KETERANGAN
function buildDescriptionWithUnit(qtyValue, unitValue, noteValue) {
    const qty = String(qtyValue ?? '').trim();
    const unit = String(unitValue ?? '').trim();
    const note = String(noteValue ?? '').trim();

    let qtyPart = '';

    if (qty && unit) {
        qtyPart = `${qty} ${unit}`;
    } else if (qty) {
        qtyPart = `${qty} unit`;
    } else if (unit) {
        qtyPart = unit;
    }

    if (qtyPart && note) {
        return `${qtyPart} - ${note}`;
    }

    return qtyPart || note;
}


// MEMISAHKAN KEMBALI QTY DAN SATUAN
function parseDescriptionUnit(descValue) {
    const raw = String(descValue ?? '').trim();

    const match = raw.match(
        /^(\d+(?:[.,]\d+)?)\s+(kg|pcs|liter|gram|ml|box|pack|unit)\s*(?:-\s*(.*))?$/i
    );

    if (match) {
        return {
            qty: match[1].replace(',', '.'),
            unit: match[2].toLowerCase() === 'unit'
                ? ''
                : match[2].toLowerCase(),
            description: match[3] || ''
        };
    }

    return {
        qty: '',
        unit: '',
        description: raw
    };
}


// MEMBUKA FORM TAMBAH
function openExpenseForm(prefill = {}) {
    document.getElementById('expenseForm').reset();

    document.getElementById('expenseId').value = '';

    document.getElementById('modalTitle').textContent =
        prefill.title
            ? 'Restock / Tambah Pengeluaran'
            : 'Tambah Pengeluaran';

    document.getElementById('btnSimpan').innerHTML =
        '<i class="bi bi-save me-1"></i> Simpan Pengeluaran';

    document.getElementById('formError').textContent = '';

    const today = new Date().toISOString().split('T')[0];

    document.getElementById('expense_date').value =
        prefill.expense_date || today;

    if (prefill.title) {
        document.getElementById('title').value = prefill.title;
    }

    if (prefill.category) {
        document.getElementById('category').value = prefill.category;
    }

    if (prefill.qty) {
        document.getElementById('qty').value = prefill.qty;
    }

    if (prefill.unit) {
        document.getElementById('unit').value = prefill.unit;
    }

    if (prefill.description) {
        document.getElementById('description').value =
            prefill.description;
    }

    expenseModal.show();
}


// TAMBAH DAN EDIT DATA
async function saveExpense(event) {
    event.preventDefault();

    const id = document.getElementById('expenseId').value;
    const button = document.getElementById('btnSimpan');
    const errorElement = document.getElementById('formError');

    const combinedDescription = buildDescriptionWithUnit(
        document.getElementById('qty').value,
        document.getElementById('unit').value,
        document.getElementById('description').value
    );

    const payload = {
        title: document.getElementById('title').value.trim(),
        category: document.getElementById('category').value,
        amount: Number(document.getElementById('amount').value),
        expense_date: document.getElementById('expense_date').value,
        description: combinedDescription
    };

    const url = id
        ? `/api/v1/expenses/${id}`
        : '/api/v1/expenses';

    const method = id ? 'PUT' : 'POST';

    button.disabled = true;
    button.textContent = 'Menyimpan...';
    errorElement.textContent = '';

    try {
        const csrfToken = getCsrfToken();

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (csrfToken) {
            headers['X-XSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(url, {
            method,
            headers,
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok) {
            const messages = result.errors
                ? Object.values(result.errors).flat().join(', ')
                : result.message;

            throw new Error(messages || 'Gagal menyimpan data.');
        }

        expenseModal.hide();

        document.getElementById('expenseForm').reset();

        await loadExpenses();

    } catch (error) {
        errorElement.textContent = error.message;

    } finally {
        button.disabled = false;

        button.innerHTML =
            '<i class="bi bi-save me-1"></i> Simpan Pengeluaran';
    }
}


// EDIT DATA
async function editExpense(id) {
    try {
        const response = await fetch(`/api/v1/expenses/${id}`, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil detail.');
        }

        const item = result.data;

        // Pengeluaran otomatis tidak boleh diedit.
        if (item.purchase_id) {
            throw new Error(
                'Pengeluaran dari transaksi pembelian tidak dapat diedit.'
            );
        }

        const parsedDesc = parseDescriptionUnit(
            item.description || ''
        );

        document.getElementById('expenseId').value = item.id;
        document.getElementById('title').value = item.title;
        document.getElementById('category').value = item.category;
        document.getElementById('qty').value = parsedDesc.qty;
        document.getElementById('unit').value = parsedDesc.unit;
        document.getElementById('amount').value = item.amount;

        document.getElementById('expense_date').value =
            String(item.expense_date).substring(0, 10);

        document.getElementById('description').value =
            parsedDesc.description;

        document.getElementById('modalTitle').textContent =
            'Edit Pengeluaran';

        document.getElementById('btnSimpan').innerHTML =
            '<i class="bi bi-save me-1"></i> Simpan Perubahan';

        document.getElementById('formError').textContent = '';

        expenseModal.show();

    } catch (error) {
        alert(error.message);
    }
}


// HAPUS DATA
async function deleteExpense(id) {
    const item = expenses.find(expense => Number(expense.id) === Number(id));

    if (item?.purchase_id) {
        alert('Pengeluaran dari pembelian tidak dapat dihapus secara terpisah.');
        return;
    }

    if (!confirm('Yakin ingin menghapus pengeluaran ini?')) {
        return;
    }

    try {
        const csrfToken = getCsrfToken();

        const headers = {
            'Accept': 'application/json'
        };

        if (csrfToken) {
            headers['X-XSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(`/api/v1/expenses/${id}`, {
            method: 'DELETE',
            headers
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal menghapus data.');
        }

        await loadExpenses();

    } catch (error) {
        alert(error.message);
    }
}

@endunless

</script>

@endsection
