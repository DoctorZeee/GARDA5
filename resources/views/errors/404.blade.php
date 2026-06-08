<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - GARDA 5</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-blue-50 flex items-center justify-center min-h-screen p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 text-center border-t-4 border-blue-500">
        <div class="text-7xl mb-6">🔍</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-3">Halaman Tidak Ditemukan</h1>
        <p class="text-gray-600 mb-8 text-lg">Maaf, halaman yang Anda tuju tidak tersedia. Tidak perlu khawatir, data kesehatan Anda tetap aman.</p>
        <a href="{{ url('/') }}" class="inline-block bg-blue-600 text-white font-bold text-xl py-4 px-8 rounded-2xl shadow-lg hover:bg-blue-700 transition w-full">
            Kembali ke Beranda Utama
        </a>
    </div>
</body>
</html>