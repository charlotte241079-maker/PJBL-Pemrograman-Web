
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Ditolak - UMKMHub</title>
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
            background: #fcebea;
            color: #b54747;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <div class="status-card shadow-sm">
        <div class="status-icon">
            <i class="bi bi-x-circle"></i>
        </div>

        <h4 class="fw-bold mb-2">Pendaftaran Belum Disetujui</h4>

        <p class="text-muted">
            Mohon maaf, pendaftaran UMKM Anda belum dapat disetujui oleh Admin.
            Silakan hubungi Admin UMKMHub untuk informasi lebih lanjut.
        </p>

        <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 mt-3">
            Kembali ke Login
        </a>
    </div>
</body>
</html>
