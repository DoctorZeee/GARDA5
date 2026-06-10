{{--
    PERUBAHAN dari versi sebelumnya:
    - Tambah link "Kelola Video" di sidebar admin
    - Tidak ada perubahan lain, semua fitur lama tetap utuh
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARDA 5 - Control Room Super Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="desktop-panel-body">

    <div class="desktop-layout-sidebar-container">
        <aside class="panel-sidebar-navigation bg-solid-dark">
            <div class="sidebar-identity">
                <i class="fa-solid fa-screwdriver-wrench panel-logo-icon text-warning"></i>
                <div>
                    <h3>GARDA 5 Console</h3>
                    <p class="role-badge admin-badge">Super Admin Mode</p>
                </div>
            </div>
            <nav class="sidebar-links-list">
                <a href="{{ route('admin.dashboard') }}" class="nav-link-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge"></i> Ruang Kendali
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i> Kelola Pengguna
                </a>
                {{-- BARU: Menu video --}}
                <a href="{{ route('admin.videos.index') }}" class="nav-link-item {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-film"></i> Kelola Video
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="nav-link-item logout-link w-100 text-start border-0 bg-transparent">
                        <i class="fa-solid fa-lock-open"></i> Log Out
                    </button>
                </form>
            </nav>
        </aside>

        <main class="panel-main-content-fluid">
            <header class="panel-top-navbar admin-top-nav">
                <div class="welcome-heading">
                    <h2>Sistem Administrasi Sentral</h2>
                    <p>Halo, {{ auth()->user()->nama_lengkap }}</p>
                </div>
            </header>

            <div class="panel-inner-scrollable-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

</body>
</html>
