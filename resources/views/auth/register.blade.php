
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - UMKMHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f1ea;
            color: #2f2a24;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .register-card {
            width: 100%;
            max-width: 650px;
            margin: auto;
            background: #fff;
            border: 1px solid #e9e4da;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(47, 42, 36, 0.06);
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #eef5ec;
            color: #3f6e49;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .form-label {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-control, .form-select {
            background-color: #f8f7f4;
            border: 1px solid #e9e4da;
            padding: 10px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #3f6e49;
            box-shadow: 0 0 0 0.2rem rgba(63, 110, 73, 0.12);
        }

        .btn-umkm {
            background-color: #3f6e49;
            border-color: #3f6e49;
            color: #fff;
            font-weight: 600;
        }

        .btn-umkm:hover {
            background-color: #335b3c;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="register-card p-4 p-md-5">

        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-mark">
                <i class="bi bi-shop"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">UMKMHub.</h5>
                <small class="text-muted">Management System</small>
            </div>
        </div>

        <h4 class="fw-bold mb-1">Daftarkan UMKM Anda</h4>
        <p class="text-muted small mb-4">
            Lengkapi informasi usaha untuk mulai menggunakan UMKMHub.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf

            <h6 class="fw-bold mb-3">Informasi Pemilik</h6>

            <div class="mb-3">
                <label class="form-label" for="owner_name">Nama Pemilik</label>
                <input
                    type="text"
                    name="owner_name"
                    id="owner_name"
                    class="form-control"
                    value="{{ old('owner_name') }}"
                    placeholder="Masukkan nama pemilik"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    placeholder="contoh@email.com"
                    required
                >
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Minimal 8 karakter"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control"
                        placeholder="Ulangi password"
                        required
                    >
                </div>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-3">Informasi Usaha</h6>

            <div class="mb-3">
                <label class="form-label" for="business_name">Nama Usaha</label>
                <input
                    type="text"
                    name="business_name"
                    id="business_name"
                    class="form-control"
                    value="{{ old('business_name') }}"
                    placeholder="Contoh: Toko Roti Makmur"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="business_type">Jenis Usaha</label>
                <select name="business_type" id="business_type" class="form-select" required>
                    <option value="">Pilih jenis usaha</option>
                    @foreach (['Kuliner', 'Fashion', 'Kerajinan', 'Pertanian', 'Perdagangan', 'Jasa', 'Lainnya'] as $type)
                        <option value="{{ $type }}" {{ old('business_type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="phone">Nomor Telepon</label>
                <input
                    type="tel"
                    name="phone"
                    id="phone"
                    class="form-control"
                    value="{{ old('phone') }}"
                    placeholder="Contoh: 081234567890"
                    required
                >
            </div>

            <div class="mb-4">
                <label class="form-label" for="address">Alamat Usaha</label>
                <textarea
                    name="address"
                    id="address"
                    class="form-control"
                    rows="3"
                    placeholder="Masukkan alamat lengkap usaha"
                    required
                >{{ old('address') }}</textarea>
            </div>

            <div class="alert alert-warning small">
                <i class="bi bi-info-circle me-1"></i>
                Setelah mendaftar, akun akan menunggu persetujuan Admin sebelum dapat digunakan.
            </div>

            <button type="submit" class="btn btn-umkm w-100 py-2">
                <i class="bi bi-person-plus me-1"></i>
                Daftar Sekarang
            </button>

            <p class="text-center small text-muted mt-4 mb-0">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-success fw-semibold text-decoration-none">
                    Masuk di sini
                </a>
            </p>
        </form>
    </div>
</body>
</html>
