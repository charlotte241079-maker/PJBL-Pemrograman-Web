@extends('layouts.app')

@section('title', 'Persetujuan Pendaftaran')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h3 class="fw-bold">Persetujuan Pendaftaran UMKM</h3>
        <p class="text-muted mb-0">
            Kelola permohonan pendaftaran UMKM yang masuk ke UMKM Hub.
        </p>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Menunggu Persetujuan</p>
                    <h3 class="fw-bold mb-0">{{ $pendaftaran->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Pendaftaran --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Daftar Pendaftar UMKM</h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama UMKM</th>
                            <th>Nama Pemilik</th>
                            <th>Jenis Usaha</th>
                            <th>No. Telepon</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pendaftaran as $index => $umkm)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $umkm->name }}</td>
                                <td>{{ $umkm->owner_name }}</td>
                                <td>{{ $umkm->business_type ?? '-' }}</td>
                                <td>{{ $umkm->phone ?? '-' }}</td>
                                <td>{{ $umkm->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form
                                            action="{{ route('admin.pendaftaran.approve', $umkm->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Setujui pendaftaran UMKM ini?')">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-success">
                                                Setujui
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('admin.pendaftaran.reject', $umkm->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Tolak pendaftaran UMKM ini?')">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-danger">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pendaftaran UMKM yang menunggu persetujuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection
