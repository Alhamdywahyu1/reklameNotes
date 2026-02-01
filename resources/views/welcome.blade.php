<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pendaftaran Reklame - DPMPTSP Bangkalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            /* Warna Profesional & Formal - Navy & Gold */
            --primary: #0f172a;
            --primary-light: #1e293b;
            --primary-dark: #020617;
            --accent: #b8860b;
            --accent-light: #d4a84b;
            --secondary: #475569;
            --success: #166534;
            --dark: #0f172a;
            --muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
        }

        /* Hero Section - Profesional */
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 40%, #1e3a5f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(184, 134, 11, 0.1);
            border-radius: 50%;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(184, 134, 11, 0.05);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-section h1 {
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.8;
        }

        .btn-hero {
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 1.05rem;
        }

        .btn-hero.btn-light {
            color: var(--primary);
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border: 2px solid var(--accent);
        }

        .btn-hero.btn-light:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent) 100%);
            color: white;
        }

        .btn-hero.btn-outline-light {
            border-width: 2px;
            border-color: var(--accent-light);
            color: var(--accent-light);
        }
        }

        .btn-hero.btn-outline-light:hover {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            border-color: var(--accent);
            transform: translateY(-2px);
            color: white;
        }

        .hero-image {
            perspective: 1000px;
        }

        .hero-image img {
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
            border-radius: 12px;
            border: 3px solid var(--accent);
        }

        /* Features Grid - Profesional */
        .features-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .features-section h2 {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--dark);
        }

        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            height: 100%;
            border-top: 4px solid var(--accent);
        }

        .feature-card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-8px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feature-card h5 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--muted);
            line-height: 1.8;
        }

        /* Process Section - Profesional */
        .process-section {
            padding: 5rem 0;
            background: white;
        }

        .process-section h2 {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--dark);
        }

        .process-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: 10px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            margin-bottom: 2rem;
            border-bottom: 4px solid var(--accent);
        }

        .process-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.35);
        }

        .process-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .process-card h5 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .process-card p {
            opacity: 0.95;
            line-height: 1.8;
        }

        .process-arrow {
            text-align: center;
            font-size: 2rem;
            color: var(--primary);
            margin: -0.5rem 0;
        }

        /* Highlight Section */
        .highlight-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f3f4f6 0%, white 100%);
        }

        .highlight-box {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--primary);
        }

        .highlight-box h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .highlight-list {
            list-style: none;
        }

        .highlight-list li {
            padding: 0.75rem 0;
            padding-left: 2rem;
            position: relative;
            color: #4b5563;
            font-weight: 500;
        }

        .highlight-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 1rem;
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .footer-content p {
            font-weight: 500;
        }

        .footer-content span {
            color: var(--secondary);
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            .features-section h2 {
                font-size: 1.8rem;
            }

            .process-section h2 {
                font-size: 1.8rem;
            }

            .btn-hero {
                padding: 0.7rem 1.5rem;
                font-size: 0.95rem;
            }

            .hero-section::before,
            .hero-section::after {
                display: none;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                min-height: auto;
                padding: 3rem 0;
            }

            .hero-section h1 {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            .hero-section p {
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
            }

            .btn-hero {
                display: block;
                width: 100%;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1><i class="bi bi-building"></i> Sistem Pendaftaran Reklame</h1>
                    <p>Platform digital resmi DPMPTSP Bangkalan untuk pendaftaran, verifikasi, dan persetujuan reklame/baliho dengan proses transparan dan efisien.</p>
                    <div>
                        <a href="{{ route('login') }}" class="btn btn-light btn-hero me-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-hero">
                            <i class="bi bi-person-plus"></i> Daftar Sekarang
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image d-none d-lg-block text-center">
                    <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo DPMPTSP" style="max-width: 300px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container-fluid">
            <h2>Fitur Unggulan</h2>
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="bi bi-file-earmark feature-icon"></i>
                        <h5>Registrasi Online</h5>
                        <p>Daftar sebagai pemohon dengan mudah melalui platform digital kami. Proses cepat dan mudah tanpa harus datang ke kantor.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="bi bi-search feature-icon"></i>
                        <h5>Tracking Real-time</h5>
                        <p>Pantau status permohonan Anda setiap saat. Dapatkan notifikasi otomatis untuk setiap perubahan status.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="bi bi-check-circle feature-icon"></i>
                        <h5>Approval Berjenjang</h5>
                        <p>Sistem approval transparan dari 3 level pejabat untuk memastikan keputusan yang adil dan profesional.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="bi bi-cloud-upload feature-icon"></i>
                        <h5>Dokumen Digital</h5>
                        <p>Upload dan kelola semua dokumen Anda dengan aman dalam satu platform terintegrasi.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="bi bi-printer feature-icon"></i>
                        <h5>Cetak Resmi</h5>
                        <p>Dapatkan surat persetujuan resmi dalam format PDF yang siap untuk dicetak dan digunakan.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="feature-card">
                        <i class="bi bi-shield-check feature-icon"></i>
                        <h5>Keamanan Terjamin</h5>
                        <p>Data Anda dilindungi dengan enkripsi tingkat enterprise dan sistem keamanan berlapis.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container-fluid">
            <h2>Alur Proses Pendaftaran</h2>
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="process-card">
                        <div class="process-number">1</div>
                        <h5>Buat Permohonan</h5>
                        <p>Isi formulir data pemohon dan detail reklame dengan lengkap dan akurat</p>
                    </div>
                    <div class="process-arrow d-none d-md-block"><i class="bi bi-arrow-down"></i></div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="process-card">
                        <div class="process-number">2</div>
                        <h5>Upload Dokumen</h5>
                        <p>Lampirkan semua dokumen yang diperlukan sesuai dengan checklist yang tersedia</p>
                    </div>
                    <div class="process-arrow d-none d-md-block"><i class="bi bi-arrow-down"></i></div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="process-card">
                        <div class="process-number">3</div>
                        <h5>Verifikasi</h5>
                        <p>Tim kami akan memverifikasi kelengkapan dokumen dan data permohonan Anda</p>
                    </div>
                    <div class="process-arrow d-none d-md-block"><i class="bi bi-arrow-down"></i></div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="process-card">
                        <div class="process-number">4</div>
                        <h5>Persetujuan</h5>
                        <p>Dapatkan surat persetujuan resmi setelah proses approval selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Highlight Section -->
    <section class="highlight-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="highlight-box">
                        <h3><i class="bi bi-star-fill"></i> Keunggulan Platform</h3>
                        <ul class="highlight-list">
                            <li>Proses pendaftaran cepat dan mudah</li>
                            <li>Transparansi penuh dalam proses approval</li>
                            <li>Notifikasi real-time untuk setiap update</li>
                            <li>Akses 24/7 dari mana saja</li>
                            <li>Dukungan teknis responsif</li>
                            <li>Sistem keamanan berlapis</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="highlight-box">
                        <h3><i class="bi bi-exclamation-circle"></i> Persyaratan Dokumen</h3>
                        <ul class="highlight-list">
                            <li>KTP/Identitas diri pemohon</li>
                            <li>Surat izin tempat usaha</li>
                            <li>Gambar desain reklame</li>
                            <li>Denah lokasi pemasangan</li>
                            <li>Surat persetujuan pemilik tanah</li>
                            <li>Bukti pembayaran biaya pendaftaran</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <div class="footer-content">
                <p>
                    <span><i class="bi bi-building"></i> DPMPTSP</span> | Sistem Pendaftaran Reklame Bangkalan
                </p>
                <p>&copy; {{ date('Y') }} Dinas Penanaman Modal dan Perizinan Terpadu Bangkalan. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>