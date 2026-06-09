<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARDA 5 - Unified Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-card shadow-sm border-0">
            <div class="login-header mb-4">
                <a href="{{ url('/') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                <h3 class="fw-bold mt-2 text-dark">Portal GARDA 5</h3>
                <p class="text-muted" style="font-size: 13px;">Masukkan Email Institusi atau NIK Anda.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" style="font-size: 13px;">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="form-login-fields">
                @csrf
                
                <div class="form-group-custom mb-3">
                    <label for="usernameInput" class="fw-bold"><i class="fa-solid fa-user"></i> ID Pengguna</label>
                    <input type="text" name="login" id="usernameInput" class="form-input-custom form-control" placeholder="NIK atau Email" value="{{ old('login') }}" required autocomplete="username">
                </div>

                <div class="form-group-custom mb-4">
                    <label for="passwordInput" class="fw-bold"><i class="fa-solid fa-lock"></i> Kata Sandi</label>
                    <div class="position-relative">
                        <input type="password" name="password" id="passwordInput" class="form-input-custom form-control pe-5" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" id="togglePasswordBtn" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent text-muted" style="z-index: 10; box-shadow: none;">
                            <i class="fa-solid fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login-submit btn w-100 py-2 fw-bold text-white" style="background-color: #1d3557;">
                    Masuk <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                </button>
            </form>
            <div class="text-center mt-4 pt-3 border-top">
                <span class="text-muted" style="font-size: 13px;">Belum memiliki akun warga?</span><br>
                <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #2a9d8f; font-size: 14px;">
                    Daftar Akun Baru
                </a>
            </div>
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        document.addEventListener("DOMContentLoaded", function() {
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('toggleIcon');

            togglePasswordBtn.addEventListener('click', function() {
                // Cek tipe input saat ini, lalu ubah ke kebalikannya
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ubah icon FontAwesome (mata terbuka vs mata dicoret)
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>