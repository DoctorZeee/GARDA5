<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GARDA 5 - Dashboard Puskesmas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="desktop-panel-body">
    <div class="desktop-layout-sidebar-container">

        <aside class="panel-sidebar-navigation puskesmas-sidebar">
            <div class="sidebar-identity">
                <i class="fa-solid fa-hospital panel-logo-icon puskesmas-accent"></i>
                <div>
                    <h3>GARDA 5</h3>
                    <p class="role-badge puskesmas-badge">Puskesmas</p>
                </div>
            </div>
            <nav class="sidebar-links-list">
                <a href="{{ route('puskesmas.dashboard') }}" class="nav-link-item {{ request()->routeIs('puskesmas.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Analitik Kesehatan
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="nav-link-item logout-link w-100 text-start border-0 bg-transparent">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </nav>
        </aside>

        <main class="panel-main-content-fluid">
            <header class="panel-top-navbar puskesmas-topbar">
                <div class="welcome-heading">
                    <h2>Portal Monitoring Kesehatan</h2>
                    <p class="puskesmas-accent">Halo, {{ auth()->user()->nama_lengkap }}</p>
                </div>
                <div>
                    <span class="badge bg-secondary">
                        <i class="fa-solid fa-clock me-1"></i>{{ now()->format('d M Y · H:i') }}
                    </span>
                </div>
            </header>

            <div class="panel-inner-scrollable-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
