
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Persetujuan - UMKMHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f1ea;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .status-card {
            max-width: 480px;
            width: 100%;
            background: white;
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #e9e4da;
        }

        .status-icon {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: #fff4dc;
            color: #b88628;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
        }

        .btn-umkm {
            background: #3f6e49;
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="status-card shadow-sm">
        <div class="status-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <h4 class="fw-bold mb-2">Pendaftaran Sedang Ditinjau</h4>

        <p class="text-muted">
            Terima kasih telah mendaftarkan usaha Anda di UMKMHub.
            Saat ini pendaftaran sedang menunggu persetujuan Admin.
        </p>

        <p class="small text-muted mb-4">
            Anda dapat masuk kembali setelah akun disetujui.
        </p>

        <a href="{{ route('login') }}" class="btn btn-umkm px-4">
            Kembali ke Login
        </a>
    </div>
</body>
</html>
