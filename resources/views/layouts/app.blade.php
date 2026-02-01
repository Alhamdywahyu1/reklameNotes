<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pendaftaran Reklame') - DPMPTSP</title>
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
            --accent-dark: #8b6914;
            --secondary: #475569;
            --success: #166534;
            --danger: #991b1b;
            --warning: #b45309;
            --info: #1e40af;
            --dark: #0f172a;
            --light: #f8fafc;
            --muted: #64748b;
            --border: #e2e8f0;
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
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: var(--dark);
        }

        /* Navbar Profesional */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, var(--primary) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: 3px solid var(--accent);
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
            color: var(--accent-light) !important;
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Sidebar Profesional */
        .sidebar-wrapper {
            display: flex;
            height: calc(100vh - 80px);
        }

        .sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            width: 260px;
            overflow-y: auto;
            box-shadow: 2px 0 15px rgba(0,0,0,0.08);
            padding: 1.5rem 0;
            border-right: 1px solid var(--border);
        }

        .sidebar .nav-link {
            color: var(--secondary);
            padding: 0.85rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            border-radius: 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0.5rem;
            border-radius: 8px;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            background-color: rgba(15, 23, 42, 0.06);
            border-left: 3px solid var(--accent);
        }

        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-left: 3px solid var(--accent);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.3);
        }

        .sidebar-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--accent-dark);
            padding: 1rem 1.5rem 0.5rem;
            letter-spacing: 1px;
        }

        /* Main Content */
        .main-content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
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
            color: var(--primary);
            font-weight: 700;
            font-size: 1.85rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-page p {
            color: var(--muted);
            margin: 0;
        }

        /* Card Profesional */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            padding: 1.15rem 1.5rem;
            font-weight: 600;
            border-bottom: 3px solid var(--accent);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Stats Card Profesional */
        .stats-card {
            text-align: center;
            padding: 1.75rem 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-top: 4px solid var(--accent);
        }

        .stats-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            transform: translateY(-4px);
        }

        .stats-card i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            color: var(--accent-dark);
        }

        .stats-card .number {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stats-card .label {
            color: var(--muted);
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
            border: 1.5px solid var(--border);
            border-radius: 6px;
            padding: 0.65rem 1rem;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
        }

        /* Alert Profesional */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
        }

        .alert-success {
            background-color: #f0fdf4;
            border-left-color: var(--success);
            color: #065f46;
        }

        .alert-danger {
            background-color: #fef2f2;
            border-left-color: var(--danger);
            color: #7f1d1d;
        }

        .alert-warning {
            background-color: #fffbeb;
            border-left-color: var(--warning);
            color: #78350f;
        }

        .alert-info {
            background-color: #eff6ff;
            border-left-color: var(--info);
            color: #0c2340;
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

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--dark) 0%, #111827 100%);
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
            border-top: none;
        }

        .footer-bottom {
            font-size: 0.9rem;
            color: #d1d5db;
        }

        .footer-brand {
            font-weight: 700;
            color: white;
            font-size: 1.1rem;
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
    </style>
    <link rel="stylesheet" href="{{ asset('css/modern-dashboard.css') }}">
    @stack('styles')
</head>
<body>
    @if (auth()->check())
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
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
            <nav class="sidebar">
                <div class="position-sticky pt-3">
                    <div class="sidebar-section-title">Menu Utama</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link @if(request()->routeIs('dashboard')) active @endif" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        @if (auth()->user()->hasRole('pemohon'))
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('permohonan.*')) active @endif" href="{{ route('permohonan.index') }}">
                                    <i class="bi bi-file-earmark-text"></i> Permohonan Saya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('permohonan.create') }}">
                                    <i class="bi bi-plus-circle"></i> Buat Permohonan
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang']))
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('approval.*')) active @endif" href="{{ route('approval.dashboard') }}">
                                    <i class="bi bi-check2-circle"></i> Approval
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('permohonan.peta')) active @endif" href="{{ route('permohonan.peta') }}">
                                    <i class="bi bi-map"></i> Peta Reklame
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->hasRole('admin'))
                            <div class="sidebar-section-title mt-4">Administrasi</div>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people"></i> Manajemen User
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if(request()->routeIs('admin.reports.*')) active @endif" href="{{ route('admin.reports.index') }}">
                                    <i class="bi bi-file-earmark-bars"></i> Laporan
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

                <div class="main-content">
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        @yield('content')
    @endif

    <!-- Footer -->
    <footer>
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="footer-brand"><i class="bi bi-building"></i> DPMPTSP</span>
                    <span class="mx-2 text-white-50">|</span>
                    <span class="footer-bottom">Sistem Pendaftaran Reklame</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-bottom mb-0">
                        &copy; {{ date('Y') }} Dinas Penanaman Modal dan Perizinan Terpadu. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
