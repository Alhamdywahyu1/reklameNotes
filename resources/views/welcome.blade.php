<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pendaftaran Reklame · DPMPTSP Bangkalan</title>
    
    <!-- Bootstrap 5 + Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Font: Inter (modern, legible) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <style>
        /* ---------------------------------------------
           TEMA BIRU MUDA – MODERN, FRESH, AER 
           --------------------------------------------- */
        :root {
            --primary-lightest: #f0f9ff;  /* latar paling ringan */
            --primary-soft: #e0f2fe;      /* biru muda untuk gradien */
            --primary-mid: #bae6fd;       /* aksen lembut */
            --primary: #0284c7;           /* biru cerah (tombol, aksen utama) */
            --primary-deep: #0369a1;      /* hover / teks kuat */
            --primary-dark: #075985;       /* footer / kontras */
            --gray-light: #f8fafc;
            --gray-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --white: #ffffff;
            --shadow-sm: 0 8px 20px rgba(2, 132, 199, 0.06);
            --shadow-hover: 0 20px 30px -8px rgba(2, 132, 199, 0.12);
            --radius-card: 1.25rem;
            --radius-btn: 2rem;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            background-color: var(--white);
            color: var(--text-dark);
        }

        /* container lebih rapi di layar besar */
        .container-custom {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* ----- HERO SECTION – GRADIEN BIRU MUDA YANG MENYEGARKAN ----- */
        .hero-section {
            background: linear-gradient(145deg, #f0faff 0%, #e6f7ff 45%, #d9f0fe 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* ornamen bulat samar – kesan modern */
        .hero-section::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -5%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(2,132,199,0.03) 0%, transparent 80%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(2,132,199,0.02) 0%, transparent 80%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 3;
        }

        .hero-section h1 {
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: #0a3b4e; /* biru tua hangat */
            margin-bottom: 1.5rem;
        }

        .hero-section h1 i {
            color: var(--primary);
            margin-right: 6px;
        }

        .hero-section p {
            font-size: 1.2rem;
            color: #2c5a6e;
            margin-bottom: 2rem;
            max-width: 90%;
            font-weight: 450;
        }

        /* tombol modern – rounded pill, soft shadow */
        .btn-hero {
            padding: 0.75rem 2.2rem;
            font-weight: 600;
            border-radius: var(--radius-btn);
            transition: all 0.25s ease;
            font-size: 1.05rem;
            border: none;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
        }

        .btn-hero.btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-hero.btn-primary:hover {
            background: var(--primary-deep);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(2, 132, 199, 0.24);
        }

        .btn-hero.btn-outline-primary {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary-deep);
        }
        .btn-hero.btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .hero-image {
            perspective: 1200px;
        }
        .hero-image img {
            max-width: 280px;
            filter: drop-shadow(0 20px 25px -5px rgba(2,132,199,0.15));
            border-radius: 20px;
            transition: transform 0.3s;
        }
        .hero-image img:hover {
            transform: rotateY(4deg) rotateX(2deg);
        }

        /* ----- FEATURES – CARD PUTIH, LEMBUT, DENGAN AKSEN BIRU ----- */
        .features-section {
            padding: 5.5rem 0;
            background: var(--white);
            scroll-margin-top: 62px;
        }

        .section-title {
            font-size: 2.4rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin-bottom: 3.5rem;
            color: #164c5e;
            position: relative;
        }
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary);
            border-radius: 4px;
            margin: 0.75rem auto 0;
        }

        .feature-card {
            background: var(--white);
            border-radius: var(--radius-card);
            padding: 2rem 1.5rem;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.2, 1);
            border: 1px solid rgba(2,132,199,0.1);
            box-shadow: var(--shadow-sm);
            text-align: center;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(2,132,199,0.25);
        }

        .feature-icon {
            font-size: 2.8rem;
            margin-bottom: 1.2rem;
            color: var(--primary);
            display: inline-block;
            background: linear-gradient(145deg, #e6f7ff, #d1eefe);
            padding: 0.6rem;
            border-radius: 18px;
        }

        .feature-card h5 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.85rem;
            color: #0e4b5e;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        /* ----- PROCESS – HORIZONTAL STEP DENGAN AKSEN BIRU LEMBUT ----- */
        .process-section {
            padding: 5.5rem 0;
            background: linear-gradient(120deg, #f6fcff, #ebf7ff);
            scroll-margin-top: 62px;
        }

        .process-card {
            background: var(--white);
            border-radius: 1.5rem;
            padding: 1.5rem 1.2rem;
            text-align: center;
            box-shadow: 0 8px 18px rgba(2,132,199,0.06);
            transition: all 0.25s;
            border-bottom: 4px solid var(--primary);
            height: 100%;
        }
        .process-card:hover {
            box-shadow: 0 20px 28px -6px rgba(2,132,199,0.16);
            transform: scale(1.02);
        }

        .process-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: var(--primary-soft);
            color: var(--primary-deep);
            border-radius: 50%;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            border: 2px solid white;
            outline: 2px solid var(--primary-mid);
        }

        .process-card h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #145a70;
        }
        .process-card p {
            color: #416d7a;
            font-size: 0.88rem;
            margin-bottom: 0;
        }

        .process-arrow {
            font-size: 2rem;
            color: var(--primary);
            opacity: 0.6;
            margin: 0.5rem 0;
        }

        /* ----- HIGHLIGHT – DAFTAR POIN DENGAN CEK LIST ----- */
        .highlight-section {
            padding: 5.5rem 0;
            background: white;
        }

        .highlight-box {
            background: #f9fdff;
            border-radius: 2rem;
            padding: 2.8rem 2.5rem;
            box-shadow: 0 8px 24px rgba(2,132,199,0.04);
            border: 1px solid rgba(2,132,199,0.12);
            height: 100%;
            transition: all 0.2s;
        }
        .highlight-box:hover {
            background: white;
            border-color: var(--primary-mid);
        }

        .highlight-box h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f4e61;
            margin-bottom: 1.8rem;
        }
        .highlight-box h3 i {
            color: var(--primary);
            margin-right: 8px;
        }

        .highlight-list {
            list-style: none;
            padding-left: 0;
        }
        .highlight-list li {
            padding: 0.7rem 0 0.7rem 2rem;
            position: relative;
            color: #2c5e6b;
            font-weight: 470;
            border-bottom: 1px dashed rgba(2,132,199,0.1);
        }
        .highlight-list li:last-child {
            border-bottom: none;
        }
        .highlight-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary);
            font-weight: 800;
            font-size: 1.2rem;
        }

        /* ----- FOOTER – BIRU TUA ELEGAN ----- */
        footer {
            background: #0b3b4c;  /* biru deep kehijauan */
            color: rgba(255,255,255,0.9);
            padding: 3rem 0;
            text-align: center;
            font-size: 0.98rem;
        }
        .footer-content span {
            font-weight: 700;
            color: #b9e6f5;
        }
        .footer-content i {
            color: #9ad4e6;
            margin-right: 4px;
        }

        /* ----- RESPONSIVE TWEAKS ----- */
        @media (max-width: 992px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .hero-image {
                margin-top: 3rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                min-height: auto;
                padding: 3rem 0;
                text-align: center;
            }
            .hero-section h1 {
                font-size: 2.1rem;
            }
            .hero-section p {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }
            .btn-hero {
                padding: 0.7rem 1.5rem;
                display: inline-block;
                width: auto;
            }
            .section-title {
                font-size: 2rem;
            }
            .process-arrow {
                transform: rotate(90deg);
            }
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 1.8rem;
            }
            .btn-hero {
                display: block;
                width: 100%;
                margin-bottom: 1rem;
            }
            .feature-card, .process-card, .highlight-box {
                padding: 1.75rem 1.25rem;
            }
        }

        /* utility tambahan */
        .bg-soft-blue {
            background-color: var(--primary-lightest);
        }
        /* ----- NAVBAR HEADER ----- */
        .navbar-custom {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 16px rgba(2,132,199,0.08);
            padding: 0.6rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: box-shadow 0.3s;
        }
        .navbar-custom.scrolled {
            box-shadow: 0 4px 24px rgba(2,132,199,0.14);
        }
        .navbar-custom .nav-link {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.98rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-btn);
            transition: all 0.2s;
        }
        .navbar-custom .nav-link:hover,
        .navbar-custom .nav-link.active {
            color: var(--primary);
            background: var(--primary-lightest);
        }
        .navbar-custom .btn-login {
            background: var(--primary);
            color: white;
            font-weight: 600;
            padding: 0.45rem 1.5rem;
            border-radius: var(--radius-btn);
            border: none;
            transition: all 0.2s;
        }
        .navbar-custom .btn-login:hover {
            background: var(--primary-deep);
            transform: translateY(-1px);
        }
        body {
            padding-top: 62px; /* offset for fixed navbar */
        }
    </style>
</head>
<body>

    <!-- HEADER NAVBAR -->
    <nav class="navbar-custom" id="mainNavbar">
        <div class="container-custom d-flex justify-content-between align-items-center">
            <a href="#hero" class="text-decoration-none d-flex align-items-center gap-2" style="color: var(--primary-deep); font-weight: 700; font-size: 1.1rem;">
                <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo" style="height: 36px;">
                DPMPTSP Bangkalan
            </a>
            <div class="d-flex align-items-center gap-1">
                <a href="#fitur" class="nav-link d-none d-md-inline-block">Daftar Fitur</a>
                <a href="#alur" class="nav-link d-none d-md-inline-block">Alur Proses</a>
                <a href="{{ route('login') }}" class="btn-login ms-2">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION – modern, biru muda, full optimasi -->
    <section class="hero-section" id="hero">
        <div class="container-custom w-100">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6 hero-content">
                    <h1>
                        <i class="bi bi-megaphone"></i> Sistem Pendaftaran Reklame
                    </h1>
                    <p class="lead">
                        Platform digital resmi DPMPTSP Bangkalan — pendaftaran, verifikasi, dan persetujuan reklame/baliho secara transparan, cepat, dan paperless.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-hero">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-hero">
                            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image d-none d-lg-block text-center">
                    <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo DPMPTSP Bangkalan" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR UNGGULAN – 6 cards dengan icon biru muda -->
    <section class="features-section" id="fitur">
        <div class="container-custom">
            <h2 class="section-title text-center">Fitur Unggulan</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h5>Registrasi Online</h5>
                        <p>Daftar sebagai pemohon dengan mudah via platform digital. Proses cepat tanpa antre.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-radar"></i>
                        </div>
                        <h5>Tracking Real-time</h5>
                        <p>Pantau status permohonan kapan saja. Notifikasi otomatis tiap perubahan.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h5>Approval Berjenjang</h5>
                        <p>Transparansi penuh dari 3 level pejabat untuk keputusan yang adil.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>
                        <h5>Dokumen Digital</h5>
                        <p>Upload & kelola semua berkas dalam satu platform terintegrasi.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-printer"></i>
                        </div>
                        <h5>Cetak Resmi</h5>
                        <p>Surat persetujuan format PDF siap cetak, legal dan terverifikasi.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5>Keamanan Terjamin</h5>
                        <p>Enkripsi enterprise & sistem keamanan berlapis, data aman.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR PROSES – 4 langkah simpel -->
    <section class="process-section" id="alur">
        <div class="container-custom">
            <h2 class="section-title text-center">Alur Proses Pendaftaran</h2>
            <div class="row g-3 justify-content-center align-items-stretch">
                <div class="col-6 col-lg-3">
                    <div class="process-card">
                        <div class="process-number">1</div>
                        <h5>Buat Permohonan</h5>
                        <p>Isi formulir data pemohon & detail reklame secara lengkap.</p>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-flex align-items-center px-0">
                    <i class="bi bi-arrow-right process-arrow"></i>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="process-card">
                        <div class="process-number">2</div>
                        <h5>Upload Dokumen</h5>
                        <p>Lampirkan dokumen sesuai checklist yang tersedia.</p>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-flex align-items-center px-0">
                    <i class="bi bi-arrow-right process-arrow"></i>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="process-card">
                        <div class="process-number">3</div>
                        <h5>Verifikasi</h5>
                        <p>Tim memverifikasi kelengkapan dan keabsahan data.</p>
                    </div>
                </div>
                <div class="col-auto d-none d-lg-flex align-items-center px-0">
                    <i class="bi bi-arrow-right process-arrow"></i>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="process-card">
                        <div class="process-number">4</div>
                        <h5>Persetujuan</h5>
                        <p>Surat resmi terbit, siap diunduh dan dicetak.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KEUNGGULAN & PERSYARATAN – dua kolom elegan -->
    <section class="highlight-section">
        <div class="container-custom">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="highlight-box">
                        <h3><i class="bi bi-stars"></i> Keunggulan Platform</h3>
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
                <div class="col-lg-6">
                    <div class="highlight-box">
                        <h3><i class="bi bi-file-check"></i> Persyaratan Dokumen</h3>
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

    <!-- FOOTER BIRU TUA DENGAN KESAN BERSIH -->
    <footer>
        <div class="container-custom">
            <div class="footer-content">
                <p class="fs-5 mb-2">
                    <span><i class="bi bi-building"></i> DPMPTSP Bangkalan</span> · Sistem Pendaftaran Reklame
                </p>
                <p class="opacity-75 mb-0">
                    &copy; {{ date('Y') }} Dinas Penanaman Modal dan Perizinan Terpadu Bangkalan. All Rights Reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for nav links
        document.querySelectorAll('.navbar-custom a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Navbar shadow on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>