<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GARDA 5 - Dashboard Warga</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="user-page-body">

    <nav class="user-topbar">
        <div class="brand">GARDA <span>5</span></div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="topbar-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </button>
        </form>
    </nav>

    <main class="user-main">
        @if(session('success'))
            <div class="user-alert user-alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="user-alert user-alert-error">❌ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

</body>
</html>
