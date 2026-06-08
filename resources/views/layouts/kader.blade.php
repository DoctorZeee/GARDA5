<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kader - GARDA 5</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-danger fw-bold"
                        style="text-decoration: none;">
                        🚪 Logout
                    </button>
                </form>
            </li>
        </ul>
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">GARDA 5 - Kader</a>
        </div>
    </nav>
    <main class="container py-4">
        @yield('content')
    </main>
</body>

</html>
