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
            --primary: #1a5490;
            --secondary: #f39c12;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: var(--primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
        }

        .sidebar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: #666;
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            color: var(--primary);
            background-color: #f8f9fa;
        }

        .sidebar .nav-link.active {
            color: var(--primary);
            border-left-color: var(--primary);
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #0f3a60;
            border-color: #0f3a60;
        }

        .badge-primary {
            background-color: var(--primary);
        }

        .status-badge {
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .main-content {
            padding: 2rem 0;
        }

        .header-page {
            margin-bottom: 2rem;
        }

        .header-page h1 {
            color: var(--primary);
            font-weight: bold;
        }

        .stats-card {
            text-align: center;
            padding: 1.5rem;
        }

        .stats-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .stats-card .label {
            color: #666;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(26, 84, 144, 0.25);
        }

        .alert {
            border: none;
            border-radius: 4px;
        }

        .pagination {
            margin-top: 1.5rem;
        }

        .pagination .page-link {
            color: var(--primary);
        }

        .pagination .page-link:hover {
            background-color: var(--primary);
            color: white;
        }

        .pagination .page-link.active {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        footer {
            background-color: var(--primary);
            color: white;
            padding: 1.5rem 0;
            margin-top: 3rem;
            border-top: none;
        }

        .footer-bottom {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-brand {
            font-weight: bold;
            color: white;
        }
    </style>
    @stack('styles')
</head>
<body>
    @if (auth()->check())
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="bi bi-building"></i> DPMPTSP - Sistem Reklame
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-2 sidebar d-md-block">
                    <div class="position-sticky pt-4">
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

                <!-- Main content -->
                <main class="col-md-10 ms-sm-auto px-md-4 main-content">
                    @if ($message = session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($message = session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endif

    <footer>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span class="footer-brand">DPMPTSP</span>
                    <span class="mx-2 text-white-50">|</span>
                    <span class="footer-bottom">Sistem Pendaftaran Reklame</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-bottom mb-0">
                        &copy; {{ date('Y') }} All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
