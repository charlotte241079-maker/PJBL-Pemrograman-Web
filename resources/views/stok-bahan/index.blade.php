@extends('layouts.app')

@section('title', 'Stok Bahan - UMKMHub')
@section('page-heading', 'Stok Bahan')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    .ingredient-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .ingredient-header h4 {
        font-weight: 700;
        margin-bottom: 6px;
    }

    .ingredient-header p {
        color: #7b7b7b;
        margin-bottom: 0;
    }

    .ingredient-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .ingredient-card .card-body {
        padding: 24px;
    }

    .ingredient-table {
        margin-bottom: 0;
        white-space: nowrap;
    }

    .ingredient-table thead th {
        background: #f8f7f3;
        color: #6d6b64;
        font-size: 13px;
        font-weight: 600;
        padding: 15px;
        border-bottom: 1px solid #eae8e1;
    }

    .ingredient-table tbody td {
        padding: 16px 15px;
        font-size: 14px;
        vertical-align: middle;
    }

    .ingredient-name {
        font-weight: 600;
        color: #37352f;
    }

    .category-badge {
        background: #f0eee7;
        color: #625b4d;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
    }

    .stock-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-normal {
        background: #e8f1e4;
        color: #54734b;
    }

    .stock-low {
        background: #fff0d9;
        color: #a66b17;
    }

    .ingredient-empty {
        padding: 45px 20px !important;
        text-align: center;
        color: #999;
    }

    .ingredient-search {
        max-width: 280px;
        width: 100%;
    }

    .ingredient-search input {
        border-radius: 8px;
        padding: 10px 14px;
    }

    .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        font-size: 13px;
        margin-top: 12px;
    }

    .modal-content {
        border: none;
        border-radius: 14px;
    }

    .modal-header {
        background: #f8f7f3;
        border-bottom: 1px solid #eae8e1;
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="ingredient-header">
        <div>
            <h4>Stok Bahan</h4>
            <p>Pengelolaan Bahan Juragan</p>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalTambahBahan"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Bahan
        </button>
    </div>

    <div class="card ingredient-card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h6 class="fw-bold mb-1">Daftar Bahan</h6>
                    <small class="text-muted">
                        Data persediaan bahan baku Juragan
                    </small>
                </div>

                <div class="ingredient-search">
                    <input
                        type="text"
                        id="searchIngredient"
                        class="form-control"
                        placeholder="Cari nama bahan..."
                    >
                </div>
            </div>

            <div class="table-responsive">
                <table id="ingredientDataTable" class="table table-hover ingredient-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bahan</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Stok Minimum</th>
                            <th>Harga Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="ingredientTable">
                        <tr>
                            <td colspan="8" class="ingredient-empty">
                                Memuat data bahan...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <small class="text-muted" id="totalIngredient">
                    Memuat jumlah bahan...
                </small>

                <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm"
                    id="btnRefresh"
                >
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Refresh
                </button>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH BAHAN --}}
<div
    class="modal fade"
    id="modalTambahBahan"
    tabindex="-1"
    aria-labelledby="modalTambahBahanLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="modalTambahBahanLabel">
                        Tambah Bahan
                    </h5>
                    <small class="text-muted">
                        Masukkan informasi bahan baru
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body">
                <form id="formTambahBahan">

                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="Contoh: Tepung Terigu"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input
                            type="text"
                            name="category"
                            class="form-control"
                            placeholder="Contoh: Bahan Kering"
                            maxlength="100"
                        >
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input
                                type="number"
                                name="stock"
                                class="form-control"
                                min="0"
                                step="0.01"
                                value="0"
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input
                                type="text"
                                name="unit"
                                class="form-control"
                                placeholder="kg, liter, pcs"
                                maxlength="50"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum</label>
                            <input
                                type="number"
                                name="min_stock"
                                class="form-control"
                                min="0"
                                step="0.01"
                                value="0"
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Terakhir</label>
                            <input
                                type="number"
                                name="last_price"
                                class="form-control"
                                min="0"
                                step="0.01"
                                value="0"
                            >
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            id="btnSimpanBahan"
                        >
                            Simpan Bahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT BAHAN --}}
<div class="modal fade" id="modalEditBahan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Bahan</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formEditBahan">

                    <input type="hidden" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="name"
                            class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category"
                            class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock"
                                class="form-control" min="0"
                                step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="unit"
                                class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum</label>
                            <input type="number" name="min_stock"
                                class="form-control" min="0"
                                step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga Terakhir</label>
                            <input type="number" name="last_price"
                                class="form-control" min="0"
                                step="0.01" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                            class="btn btn-primary"
                            id="btnUpdateBahan">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const API_URL = '/api/v1/ingredients';

    const table = document.getElementById('ingredientTable');
    const totalIngredient = document.getElementById('totalIngredient');
    const searchInput = document.getElementById('searchIngredient');
    const btnRefresh = document.getElementById('btnRefresh');
    const formTambah = document.getElementById('formTambahBahan');
    const btnSimpan = document.getElementById('btnSimpanBahan');
    const modalElement = document.getElementById('modalTambahBahan');
    const formEdit = document.getElementById('formEditBahan');
    const modalEdit = new bootstrap.Modal(document.getElementById('modalEditBahan'));

    let ingredientData = [];
    let dataTableInstance = null;

    function escapeHTML(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value ?? 0).toLocaleString('id-ID');
    }

    function destroyDataTableIfExists() {
        if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#ingredientDataTable')) {
            $('#ingredientDataTable').DataTable().destroy();
            dataTableInstance = null;
        }
    }

    function initDataTable() {
        if (!window.jQuery || !$.fn.DataTable) return;

        destroyDataTableIfExists();

        const dt = $('#ingredientDataTable').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, searchable: false, targets: 0 },
                { orderable: false, targets: -1 }
            ],
            language: {
                lengthMenu: 'Tampilkan _MENU_ bahan',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ bahan',
                infoEmpty: 'Belum ada data bahan',
                zeroRecords: 'Bahan tidak ditemukan',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                }
            }
        });

        dataTableInstance = dt;

        dt.on('draw.dt', function () {
            if (!dataTableInstance) return;
            const info = dataTableInstance.page.info();
            if (!info) return;

            dt.column(0, { search: 'applied', order: 'applied' })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = info.start + i + 1;
                });
        });

        if (searchInput && searchInput.value) {
            dt.search(searchInput.value);
        }

        dt.draw();
    }
    function showMessage(message, type = 'success') {
        let box = document.getElementById('ingredientMessage');

        if (!box) {
            box = document.createElement('div');
            box.id = 'ingredientMessage';
            table.closest('.card-body').prepend(box);
        }

        box.className = `alert alert-${type} alert-dismissible fade show`;
        box.classList.remove('d-none');
        box.textContent = message;

        setTimeout(() => {
            box.classList.add('d-none');
        }, 3500);
    }

    function renderIngredients(data) {
        destroyDataTableIfExists();
        table.innerHTML = '';

        totalIngredient.textContent = 'Total ' + data.length + ' bahan';

        if (!Array.isArray(data) || data.length === 0) {
            table.innerHTML = `
                <tr>
                    <td colspan="8" class="ingredient-empty">
                        <i class="bi bi-box-seam fs-3"></i>
                        <div class="mt-2">Belum ada data bahan.</div>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(function (item, index) {
            const stock = Number(item.stock ?? 0);
            const minStock = Number(item.min_stock ?? 0);

            const stockClass = stock <= minStock ? 'stock-low' : 'stock-normal';
            const stockLabel = stock <= minStock ? 'Stok Menipis' : 'Aman';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>
                    <span class="ingredient-name">
                        ${escapeHTML(item.name)}
                    </span>
                </td>
                <td>
                    <span class="category-badge">
                        ${escapeHTML(item.category ?? '-')}
                    </span>
                </td>
                <td>
                    <span class="stock-badge ${stockClass}">
                        ${stock}
                    </span>
                    <div>
                        <small class="text-muted">
                            ${stockLabel}
                        </small>
                    </div>
                </td>
                <td>${escapeHTML(item.unit ?? '-')}</td>
                <td>${minStock}</td>
                <td>${formatRupiah(item.last_price)}</td>
                <td>
                    <div class="d-flex gap-2">
                        <button type="button"
                            class="btn btn-sm btn-outline-primary btn-edit"
                            data-id="${item.id}"
                            title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-danger btn-delete"
                            data-id="${item.id}"
                            title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            table.appendChild(row);
        });

        initDataTable();
    }

    async function loadIngredients() {
        destroyDataTableIfExists();
        table.innerHTML = `
            <tr>
                <td colspan="8" class="ingredient-empty">
                    <div class="spinner-border spinner-border-sm text-secondary mb-2"></div>
                    <div>Memuat data bahan...</div>
                </td>
            </tr>
        `;

        try {
            const response = await fetch(API_URL, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();
            ingredientData = Array.isArray(result.data)
                ? result.data
                : (Array.isArray(result) ? result : []);

            renderIngredients(ingredientData);
        } catch (error) {
            console.error('Gagal mengambil data:', error);
            destroyDataTableIfExists();

            table.innerHTML = `
                <tr>
                    <td colspan="8" class="ingredient-empty text-danger">
                        <i class="bi bi-exclamation-circle fs-4"></i>
                        <div class="mt-2">
                            Gagal mengambil data dari API. (${escapeHTML(error.message)})
                        </div>
                    </td>
                </tr>
            `;

            totalIngredient.textContent = 'Data tidak tersedia';
        }
    }

    table.addEventListener('click', async function (event) {
        const editButton = event.target.closest('.btn-edit');
        const deleteButton = event.target.closest('.btn-delete');

        if (editButton) {
            const id = editButton.dataset.id;
            const item = ingredientData.find(data => String(data.id) === String(id));
            if (!item) return;

            formEdit.elements['id'].value = item.id;
            formEdit.elements['name'].value = item.name ?? '';
            formEdit.elements['category'].value = item.category ?? '';
            formEdit.elements['stock'].value = item.stock ?? 0;
            formEdit.elements['unit'].value = item.unit ?? '';
            formEdit.elements['min_stock'].value = item.min_stock ?? 0;
            formEdit.elements['last_price'].value = item.last_price ?? 0;

            modalEdit.show();
        }

        if (deleteButton) {
            const id = deleteButton.dataset.id;
            const item = ingredientData.find(data => String(data.id) === String(id));
            if (!item) return;

            const yakin = confirm(`Apakah kamu yakin ingin menghapus ${item.name}?`);
            if (!yakin) return;

            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal menghapus bahan.');
                }

                showMessage(result.message || 'Bahan berhasil dihapus!', 'success');
                await loadIngredients();
            } catch (error) {
                console.error(error);
                showMessage(error.message, 'danger');
            }
        }
    });

    formEdit.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!formEdit.reportValidity()) return;

        const id = formEdit.elements['id'].value;
        const formData = new FormData(formEdit);

        const data = {
            name: formData.get('name'),
            category: formData.get('category') || null,
            stock: Number(formData.get('stock')),
            unit: formData.get('unit'),
            min_stock: Number(formData.get('min_stock')),
            last_price: Number(formData.get('last_price'))
        };

        const button = document.getElementById('btnUpdateBahan');
        button.disabled = true;
        button.textContent = 'Menyimpan...';

        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat().join('\n');
                throw new Error(errors || result.message || 'Gagal memperbarui bahan.');
            }

            modalEdit.hide();
            showMessage(result.message || 'Bahan berhasil diperbarui!', 'success');
            await loadIngredients();
        } catch (error) {
            console.error(error);
            showMessage(error.message, 'danger');
        } finally {
            button.disabled = false;
            button.textContent = 'Simpan Perubahan';
        }
    });

    formTambah.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!formTambah.reportValidity()) return;

        const formData = new FormData(formTambah);

        const data = {
            name: formData.get('name'),
            category: formData.get('category') || null,
            stock: Number(formData.get('stock') || 0),
            unit: formData.get('unit'),
            min_stock: Number(formData.get('min_stock') || 0),
            last_price: Number(formData.get('last_price') || 0)
        };

        btnSimpan.disabled = true;
        btnSimpan.textContent = 'Menyimpan...';

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                if (response.status === 422) {
                    const errors = Object.values(result.errors || {}).flat().join('\n');
                    alert(errors || result.message || 'Data tidak valid.');
                } else {
                    alert(result.message || 'Gagal menyimpan bahan.');
                }
                return;
            }

            alert(result.message || 'Bahan berhasil ditambahkan!');
            formTambah.reset();

            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }

            await loadIngredients();
        } catch (error) {
            console.error('Error menyimpan bahan:', error);
            alert('Terjadi kesalahan saat menghubungi server.');
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.textContent = 'Simpan Bahan';
        }
    });

    searchInput.addEventListener('input', function () {
        if (dataTableInstance) {
            dataTableInstance.search(this.value).draw();
        }
    });

    btnRefresh.addEventListener('click', loadIngredients);

    loadIngredients();
});
</script>
@endsection