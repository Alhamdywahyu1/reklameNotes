<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pendaftaran Reklame') - DPMPTSP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Tema Biru Muda Modern - DPMPTSP Bangkalan */
            --primary-lightest: #f0f9ff;
            --primary-soft: #e0f2fe;
            --primary-mid: #bae6fd;
            --primary-light: #38bdf8;
            --primary: #0284c7;
            --primary-deep: #0369a1;
            --primary-dark: #075985;
            --gray-light: #f8fafc;
            --gray-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #475569;
            --white: #ffffff;
            --shadow-sm: 0 8px 20px rgba(2, 132, 199, 0.06);
            --shadow-hover: 0 20px 30px -8px rgba(2, 132, 199, 0.12);
            --radius-card: 1.25rem;
            --radius-btn: 2rem;
            --success: #166534;
            --danger: #991b1b;
            --warning: #b45309;
            --info: #1e40af;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(120deg, #f6fcff, #ebf7ff);
            color: var(--text-dark);
        }

        /* Navbar Modern Biru Muda */
        .navbar {
            background: linear-gradient(135deg, #075985 0%, #0369a1 100%);
            box-shadow: var(--shadow-sm);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: none;
        }

        .navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white !important;
        }

        .navbar .navbar-brand img {
            border-radius: 6px;
            transition: transform 0.3s;
            border: 2px solid var(--accent);
        }

        .navbar .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .navbar .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            transition: all 0.3s;
            border-radius: 6px;
            margin: 0 4px;
            padding: 0.5rem 1rem;
        }

        .navbar .nav-link:hover {
            color: #b9e6f5 !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Sidebar Profesional */
        .sidebar-wrapper {
            display: flex;
            height: calc(100vh - 80px);
            overflow: hidden;
            position: relative;
        }

        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            width: 260px;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 2px 0 15px rgba(2, 132, 199, 0.06);
            padding: 1.5rem 0;
            border-right: 1px solid var(--gray-border);
            transition: width 0.3s ease;
            position: relative;
            z-index: 10;
            flex-shrink: 0;
        }

        .sidebar.collapsed {
            width: 76px;
        }

        .sidebar .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem 0.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar .sidebar-header .sidebar-section-title {
            padding: 0;
            margin: 0;
        }

        .sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar.collapsed .sidebar-section-title {
            display: none;
        }

        .sidebar .nav-link {
            color: var(--text-muted);
            padding: 0.85rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0.5rem;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-link {
            padding: 0.85rem 0;
            justify-content: center;
            margin: 0 0.5rem;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        .sidebar .nav-link i {
            width: 22px;
            text-align: center;
            font-size: 1.25rem;
            margin: 0;
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            background-color: rgba(2, 132, 199, 0.06);
            border-left: 3px solid var(--primary);
        }

        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-deep) 100%);
            border-left: 3px solid var(--primary);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--primary-deep);
            padding: 1rem 1.5rem 0.5rem;
            letter-spacing: 1px;
        }

        /* Main Content */
        .main-content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background: linear-gradient(120deg, #f6fcff, #ebf7ff);
            transition: width 0.3s ease;
        }

        .main-content {
            padding: 0;
        }

        /* Header Page */
        .header-page {
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border);
        }

        .header-page h1 {
            color: #0a3b4e;
            font-weight: 700;
            font-size: 1.85rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-page p {
            color: var(--text-muted);
            margin: 0;
        }

        /* Card Modern */
        .card {
            border: 1px solid rgba(2, 132, 199, 0.1);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.3s cubic-bezier(0.16, 1, 0.2, 1), transform 0.3s cubic-bezier(0.16, 1, 0.2, 1), border-color 0.3s cubic-bezier(0.16, 1, 0.2, 1);
            overflow: hidden;
            background: white;
        }

        .card:not(:has(canvas)):hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
            border-color: rgba(2, 132, 199, 0.25);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-deep) 100%);
            color: white;
            border: none;
            padding: 1.15rem 1.5rem;
            font-weight: 600;
            border-bottom: none;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats Card Modern */
        .stats-card {
            text-align: center;
            padding: 1.75rem 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
            transition: all 0.25s;
            border-top: 4px solid var(--primary);
        }

        .stats-card:hover {
            box-shadow: var(--shadow-hover);
            transform: scale(1.02);
        }

        .stats-card i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            color: var(--primary);
        }

        .stats-card .number {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--primary-deep);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stats-card .label {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Buttons Profesional */
        .btn {
            border-radius: 6px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: 1px solid var(--primary-dark);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.35);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            color: var(--primary);
            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
            color: var(--primary);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #15803d 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #b91c1c 100%);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            color: white;
        }

        /* Form Controls */
        .form-control, .form-select {
            border: 1.5px solid var(--gray-border);
            border-radius: 0.75rem;
            padding: 0.65rem 1rem;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        }

        /* Alert Modern */
        .alert {
            border: none;
            border-radius: 0.75rem;
            border-left: 4px solid;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background-color: #ecfdf5;
            border-left-color: #10b981;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fef2f2;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        .alert-warning {
            background-color: #fffbeb;
            border-left-color: #f59e0b;
            color: #92400e;
        }

        .alert-info {
            background-color: #f0f9ff;
            border-left-color: var(--primary);
            color: #0c4a6e;
        }

        /* Table */
        .table {
            border-collapse: collapse;
        }

        .table thead {
            background-color: #f3f4f6;
            font-weight: 600;
            color: var(--dark);
        }

        .table thead th {
            border: none;
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Badge */
        .badge {
            border-radius: 6px;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Pagination */
        .pagination {
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-link {
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            color: var(--primary);
            font-weight: 600;
        }

        .page-link:hover {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-link.active {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -260px;
                top: 80px;
                height: calc(100vh - 80px);
                z-index: 1020;
                transition: left 0.3s;
                width: 260px;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-wrapper {
                height: auto;
            }

            .main-content-wrapper {
                padding: 1rem;
            }

            .header-page h1 {
                font-size: 1.5rem;
            }

            .stats-card {
                padding: 1.5rem 1rem;
            }

            .stats-card i {
                font-size: 2rem;
            }

            .stats-card .number {
                font-size: 2rem;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Footer Modern Biru Tua (Gaya Mamikos Dark Theme) */
        footer {
            background: #0b3b4c; /* biru deep kehijauan */
            color: rgba(255,255,255,0.85); /* teks putih transparan */
            padding: 4rem 0 0rem;
            border-top: none;
            font-size: 0.95rem;
            text-align: left;
            margin-top: 4rem;
        }
        
        .footer-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff; /* teks putih tegas */
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-list li {
            margin-bottom: 0.85rem;
        }

        .footer-list a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }

        .footer-list a:hover {
            color: #b9e6f5;
        }

        .footer-contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1rem;
            color: rgba(255,255,255,0.85);
            font-weight: 500;
        }

        .footer-socials {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
        }

        .footer-socials a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            border-radius: 50%;
            transition: all 0.2s;
            font-size: 1.1rem;
        }

        .footer-socials a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .store-buttons {
            display: flex;
            gap: 10px;
            margin-top: 1.5rem;
        }

        .store-buttons img {
            height: 40px;
            border-radius: 6px;
            transition: transform 0.2s;
        }

        .store-buttons img:hover {
            transform: scale(1.05);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/modern-dashboard.css') }}">
    @stack('styles')
</head>
<body>
    @if (auth()->check())
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button class="btn btn-link text-white border-0 d-md-none me-2" id="mobileSidebarBtn" title="Buka Sidebar">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo Bangkalan" style="height: 40px; border-radius: 6px;">
                    <span>DPMPTSP</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                                @if(auth()->user()->hasRole('operator'))
                                <li><a class="dropdown-item" href="{{ route('profile.login-history') }}"><i class="bi bi-clock-history"></i> Riwayat Login</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Layout -->
        <div class="sidebar-wrapper">
            <!-- Sidebar -->
            <nav class="sidebar" id="mainSidebar">
                <script>
                    // Cegah efek kedip/animasi (FOUC) saat berpindah halaman dengan memberi status tertutup sedini mungkin
                    if (localStorage.getItem('sidebar_collapsed') === 'true') {
                        document.getElementById('mainSidebar').classList.add('collapsed');
                    }
                </script>
                <div class="position-sticky pt-3">
                    <div class="sidebar-header">
                        <div class="sidebar-section-title">Menu Utama</div>
                        <button class="btn btn-sm btn-outline-secondary border-0 text-muted" id="toggleSidebarBtn" title="Tutup/Buka Sidebar">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                    </div>
                    <ul class="nav flex-column mt-0">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span>
                            </a>
                        </li>

                        @if (auth()->user()->hasRole('pemohon'))
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('permohonan.*')) active @endif" href="{{ route('permohonan.index') }}">
                                    <i class="bi bi-file-earmark-text"></i> <span class="nav-text">Permohonan Saya</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('permohonan.create') }}">
                                    <i class="bi bi-plus-circle"></i> <span class="nav-text">Buat Permohonan</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang']))
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('approval.dashboard')) active @endif" href="{{ route('approval.dashboard') }}">
                                    <i class="bi bi-check2-circle"></i> <span class="nav-text">Approval</span>
                                </a>
                            </li>
                            @if (auth()->user()->hasAnyRole(['kepala_seksi', 'kepala_bidang']))
                                <li class="nav-item">
                                    <a class="nav-link @if(request()->routeIs('approval.revisi')) active @endif" href="{{ route('approval.revisi') }}">
                                        <i class="bi bi-pencil-square"></i> <span class="nav-text">Revisi</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('permohonan.peta')) active @endif" href="{{ route('permohonan.peta') }}">
                                    <i class="bi bi-map"></i> <span class="nav-text">Peta Reklame</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasRole('admin'))
                            <div class="sidebar-section-title mt-4">Administrasi</div>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people"></i> <span class="nav-text">Manajemen User</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('admin.reports.*')) active @endif" href="{{ route('admin.reports.index') }}">
                                    <i class="bi bi-file-earmark-bars"></i> <span class="nav-text">Laporan</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main-content-wrapper">
                @if ($message = session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <div>{{ $message }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($message = session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <div>{{ $message }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="main-content container-xl">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        @yield('content')
    @endif

    <!-- FOOTER MODERN (GAYA MAMIKOS TAPI DARK THEME) -->
    <footer>
        <div class="container-fluid px-4">
            <div class="row g-4">
                <!-- Kolom 1: Logo & Info -->
                <div class="col-lg-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo" style="height: 48px;">
                        <span class="fs-4 fw-bold text-white" style="letter-spacing: -0.5px;">DPMPTSP</span>
                    </div>
                    <p class="mb-2" style="font-size: 1rem; color: rgba(255,255,255,0.85);">Dapatkan "Info Pendaftaran Reklame" terbaru hanya di Sistem Kami.</p>
                    <p class="mb-0 fw-medium text-white">Mau "Daftar Reklame Mudah"?</p>
                    
                    <!-- Tombol Download -->
                    <div class="store-buttons mt-3 row">
                        <div class="col-auto">
                            <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_ENs.svg/320px-Google_Play_Store_badge_ENs.svg.png" style="height: 38px; width: auto;" alt="Google Play"></a>
                        </div>
                        <div class="col-auto">
                            <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Download_on_the_App_Store_Badge.svg/320px-Download_on_the_App_Store_Badge.svg.png" style="height: 38px; width: auto;" alt="App Store"></a>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Menu / Informasi -->
                <div class="col-lg-3 col-md-4 mb-4 ps-lg-4">
                    <h5 class="footer-heading">INFORMASI</h5>
                    <ul class="footer-list">
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Berita & Pengumuman</a></li>
                        <li><a href="#">Panduan Pendaftaran</a></li>
                        <li><a href="#">Pusat Bantuan</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kebijakan -->
                <div class="col-lg-2 col-md-4 mb-4">
                    <h5 class="footer-heading">KEBIJAKAN</h5>
                    <ul class="footer-list">
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Syarat dan Ketentuan<br>Umum</a></li>
                    </ul>
                </div>

                <!-- Kolom 4: Hubungi Kami -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <h5 class="footer-heading">HUBUNGI KAMI</h5>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope"></i> dpmptsp@bangkalankab.go.id
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-whatsapp"></i> +6281325111171
                    </div>
                    <div class="footer-socials">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom: Sertifikasi & Copyright -->
            <div class="footer-bottom pb-4 border-top" style="border-color: rgba(255,255,255,0.1) !important; margin-top: 3rem; padding-top: 2rem;">
                <div class="text-end w-100" style="color: rgba(255,255,255,0.7); font-weight: 500;">
                    &copy; {{ date('Y') }} DPMPTSP Bangkalan. All rights reserved
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script for Sidebar Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const mobileBtn = document.getElementById('mobileSidebarBtn');
            const sidebar = document.getElementById('mainSidebar');

            if (sidebar) {
                const toggleSidebar = function () {
                    sidebar.classList.toggle('collapsed');
                    // Simpan status baru ke localStorage
                    localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
                };
                
                const mobileToggleSidebar = function () {
                    sidebar.classList.toggle('show');
                };

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', toggleSidebar);
                }
                
                if (mobileBtn) {
                    mobileBtn.addEventListener('click', mobileToggleSidebar);
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
