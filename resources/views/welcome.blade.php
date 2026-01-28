<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pendaftaran Reklame - DPMPTSP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; }
    </style>
</head>
<body>
<div style="background: linear-gradient(135deg, #1a5490 0%, #0f3a60 100%); min-height: 100vh; color: white;">
    <div class="container py-5">
        <div class="row align-items-center" style="min-height: 80vh;">
            <div class="col-lg-6">
                <h1 style="font-size: 3.5rem; font-weight: bold; margin-bottom: 20px;">
                    <i class="bi bi-building"></i> Sistem Pendaftaran Reklame
                </h1>
                <p style="font-size: 1.3rem; margin-bottom: 30px; opacity: 0.9;">
                    Platform resmi DPMPTSP untuk pendaftaran, pemeriksaan, dan persetujuan reklame/baliho
                </p>
                <div>
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg me-2" style="color: #1a5490;">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus"></i> Daftar
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-lg" style="border: none;">
                    <div class="card-body p-5">
                        <h5 style="color: #1a5490; margin-bottom: 20px;">
                            <i class="bi bi-info-circle"></i> Fitur Utama
                        </h5>
                        <ul style="list-style: none; padding: 0;">
                            <li style="margin-bottom: 15px;">
                                <i class="bi bi-check-circle" style="color: #198754; margin-right: 10px;"></i>
                                <strong>Registrasi Online</strong> - Daftar sebagai pemohon dengan mudah
                            </li>
                            <li style="margin-bottom: 15px;">
                                <i class="bi bi-check-circle" style="color: #198754; margin-right: 10px;"></i>
                                <strong>Tracking Status</strong> - Pantau permohonan Anda secara real-time
                            </li>
                            <li style="margin-bottom: 15px;">
                                <i class="bi bi-check-circle" style="color: #198754; margin-right: 10px;"></i>
                                <strong>Approval Berjenjang</strong> - Proses approval dari 3 level pejabat
                            </li>
                            <li style="margin-bottom: 15px;">
                                <i class="bi bi-check-circle" style="color: #198754; margin-right: 10px;"></i>
                                <strong>Dokumen Digital</strong> - Upload dan kelola dokumen dengan aman
                            </li>
                            <li style="margin-bottom: 15px;">
                                <i class="bi bi-check-circle" style="color: #198754; margin-right: 10px;"></i>
                                <strong>Print Resmi</strong> - Cetak surat persetujuan dalam format PDF
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-file-earmark" style="font-size: 3rem; color: #1a5490; display: block; margin-bottom: 15px;"></i>
                    <h5>Buat Permohonan</h5>
                    <p class="text-muted">Isi formulir data pemohon dan data reklame dengan lengkap</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #198754; display: block; margin-bottom: 15px;"></i>
                    <h5>Verifikasi Dokumen</h5>
                    <p class="text-muted">Tim verifikasi kami akan memeriksa kelengkapan dokumen Anda</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-printer" style="font-size: 3rem; color: #0d6efd; display: block; margin-bottom: 15px;"></i>
                    <h5>Cetak Surat</h5>
                    <p class="text-muted">Dapatkan surat persetujuan resmi dalam format PDF</p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer style="background-color: #1a5490; color: white; padding: 20px 0; text-align: center;">
    <div class="container">
        <p>&copy; 2026 DPMPTSP. Sistem Pendaftaran Reklame. Semua hak dilindungi.</p>
    </div>
</footer>

