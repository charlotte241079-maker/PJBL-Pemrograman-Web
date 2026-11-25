
@extends('layouts.app')

@section('title', 'Catatan - UMKM Manager')
@section('page-heading', 'Catatan')

{{-- =========================
     CSS KHUSUS CATATAN
========================= --}}
@section('styles')

<link
    href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
    rel="stylesheet"
>

<style>
    /* Header halaman */
    .notes-header {
        margin-bottom: 24px;
    }

    /* Card utama */
    .notes-card {
        background: #ffffff;
        border: 1px solid #e9edf2;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
    }

    .notes-card-body {
        padding: 24px;
    }

    /* Area editor */
    #editor {
        background: #ffffff;
        min-height: 250px;
    }

    #editor .ql-editor {
        min-height: 250px;
        padding: 16px;
        font-size: 14px;
        line-height: 1.7;
        color: #334155;
    }

    #editor .ql-editor.ql-blank::before {
        color: #9ca3af;
        font-style: normal;
    }

    /* Toolbar */
    #toolbar {
        background: #f8fafc;
        border: 1px solid #dfe5ec;
        border-radius: 8px 8px 0 0;
    }

    .ql-toolbar.ql-snow {
        border-radius: 8px 8px 0 0;
    }

    .ql-container.ql-snow {
        border-radius: 0 0 8px 8px;
        font-family: Arial, sans-serif;
        font-size: 14px;
    }

    /* Daftar catatan */
    .note-item {
        background: #ffffff;
        border: 1px solid #e9edf2;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 14px;
        transition: border-color 0.2s ease;
    }

    .note-item:hover {
        border-color: #cbd5e1;
    }

    .note-content {
        line-height: 1.7;
        overflow-wrap: break-word;
        color: #334155;
    }

    .note-content img {
        max-width: 100%;
    }

    .note-content table {
        width: 100%;
        border-collapse: collapse;
    }

    .note-content td,
    .note-content th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    /* Responsif */
    @media (max-width: 768px) {
        .notes-card-body {
            padding: 16px;
        }

        #editor .ql-editor {
            min-height: 200px;
        }
    }
</style>

@endsection


{{-- =========================
     KONTEN HALAMAN
========================= --}}
@section('content')

<div class="container-fluid px-0">

    {{-- HEADER --}}
    <div class="notes-header">
        <h4 class="fw-bold mb-1">
            Catatan Operasional
        </h4>

        <p class="text-muted mb-0">
            Masukin Catatan Penting Ke Sini, Juragan!
        </p>
    </div>


    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- NOTIFIKASI ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif


    {{-- =========================
         FORM BUAT CATATAN
    ========================= --}}
    <div class="notes-card">
        <div class="notes-card-body">

            <h5 class="fw-bold mb-4">
                Buat Catatan Baru
            </h5>

            <form
                action="{{ route('notes.store') }}"
                method="POST"
                id="noteForm"
            >
                @csrf

                {{-- JUDUL --}}
                <div class="mb-4">

                    <label
                        for="title"
                        class="form-label fw-semibold"
                    >
                        Judul Catatan
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        placeholder="Masukkan judul catatan..."
                        value="{{ old('title') }}"
                        maxlength="255"
                        required
                    >

                </div>


                {{-- ISI CATATAN --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Isi Catatan
                    </label>


                    {{-- TOOLBAR QUILL --}}
                    <div id="toolbar">

                        {{-- Heading --}}
                        <span class="ql-formats">
                            <select class="ql-header">
                                <option selected></option>
                                <option value="1"></option>
                                <option value="2"></option>
                                <option value="3"></option>
                            </select>
                        </span>


                        {{-- Format teks --}}
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>


                        {{-- List --}}
                        <span class="ql-formats">
                            <button
                                class="ql-list"
                                value="ordered"
                            ></button>

                            <button
                                class="ql-list"
                                value="bullet"
                            ></button>
                        </span>


                        {{-- Alignment --}}
                        <span class="ql-formats">
                            <select class="ql-align"></select>
                        </span>


                        {{-- Link dan reset format --}}
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-clean"></button>
                        </span>

                    </div>


                    {{-- AREA EDITOR --}}
                    <div id="editor"></div>


                    {{-- FIELD UNTUK DIKIRIM KE LARAVEL --}}
                    <textarea
                        name="content"
                        id="content"
                        hidden
                    ></textarea>

                </div>


                {{-- TOMBOL SIMPAN --}}
                <div class="d-flex justify-content-end mt-4">

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >
                        <i class="bi bi-save me-1"></i>
                        Simpan Catatan
                    </button>

                </div>

            </form>

        </div>
    </div>



    {{-- =========================
         DAFTAR CATATAN
    ========================= --}}
    <div class="notes-card">
        <div class="notes-card-body">

            <h5 class="fw-bold mb-4">
                Catatan Tersimpan
            </h5>


            @forelse($notes as $note)

                <div class="note-item">

                    {{-- JUDUL DAN TANGGAL --}}
                    <div class="d-flex justify-content-between
                                align-items-start gap-3">

                        <div>
                            <h6 class="fw-bold mb-1">
                                {{ $note->title }}
                            </h6>

                            <small class="text-muted">
                                {{ $note->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>


                        {{-- TOMBOL HAPUS --}}
                        <form
                            action="{{ route('notes.destroy', $note->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus catatan ini?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-outline-danger btn-sm"
                            >
                                <i class="bi bi-trash"></i>
                                Hapus
                            </button>

                        </form>

                    </div>


                    <hr>


                    {{-- ISI CATATAN --}}
                    <div class="note-content">
                        {!! $note->content !!}
                    </div>

                </div>

            @empty

                {{-- KETIKA BELUM ADA CATATAN --}}
                <div class="text-center text-muted py-5">

                    <i class="bi bi-journal-text fs-1"></i>

                    <p class="mt-2 mb-0">
                        Belum ada catatan yang tersimpan.
                    </p>

                </div>

            @endforelse

        </div>
    </div>

</div>

@endsection



{{-- =========================
     JAVASCRIPT QUILL
========================= --}}

@section('scripts')

<script src="https://unpkg.com/quill@2.0.3/dist/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    console.log('Script Catatan berjalan');

    console.log('Quill:', typeof Quill);

    if (typeof Quill === 'undefined') {
        console.error('Quill belum berhasil dimuat!');
        return;
    }

    const editorElement = document.getElementById('editor');
    const form = document.getElementById('noteForm');
    const contentInput = document.getElementById('content');

    if (!editorElement || !form || !contentInput) {
        console.error('Elemen editor tidak ditemukan!');
        return;
    }

    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Tuliskan catatan operasional UMKM di sini...',
        modules: {
            toolbar: '#toolbar'
        }
    });

    console.log('Quill berhasil diinisialisasi!');

    form.addEventListener('submit', function (event) {

        const text = quill.getText().trim();

        if (text.length === 0) {
            event.preventDefault();
            alert('Isi catatan tidak boleh kosong!');
            return;
        }

        contentInput.value = quill.root.innerHTML;

    });

});
</script>

@endsection
