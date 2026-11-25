<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - UMKMHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f1ea;
            color: #2f2a24;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
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
        .btn-umkm {
            background-color: #3f6e49;
            border-color: #3f6e49;
            color: #fff;
            font-weight: 600;
        }
        .btn-umkm:hover {
            background-color: #335b3c;
            border-color: #335b3c;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-mark">
                <i class="bi bi-shop"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">UMKMHub.</h5>
                <small class="text-muted">Management System</small>
            </div>
        </div>

        <h4 class="fw-bold mb-1">Selamat Datang, Juragan!</h4>
        <p class="text-muted small mb-4">Masuk untuk mengelola stok, transaksi, dan analisis usaha.</p>

        @if($errors->any())
            <div class="alert alert-danger py-2 small border-0">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="form-control bg-light border-0 py-2" placeholder="admin@umkmhub.id" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold">Password</label>
                <input type="password" id="password" name="password"
                       class="form-control bg-light border-0 py-2" placeholder="••••••••" required>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn btn-umkm w-100 py-2">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Dashboard
            </button>
        </form>
    </div>
</body>
</html>