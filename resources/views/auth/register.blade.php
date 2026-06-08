<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARDA 5 - Pendaftaran Warga</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="login-body" style="height: auto; padding: 40px 15px;">
    <div class="container d-flex justify-content-center">
        <div class="login-card shadow border-0" style="width: 100%; max-width: 700px; padding: 40px;">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Daftar Akun Warga</h3>
                <p class="text-muted">Bergabunglah dengan GARDA 5 untuk memantau kesehatan Anda.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">NIK (16 Digit)</label>
                        <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                            required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Email Aktif</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Kata Sandi (Min. 8 Karakter)</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control" max="{{ date('Y-m-d') }}"
                            value="{{ old('tanggal_lahir') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Wilayah (Area Observasi)</label>
                        <select name="wilayah_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Wilayah Anda --</option>
                            @foreach ($wilayahs as $wil)
                                <option value="{{ $wil->id }}"
                                    {{ old('wilayah_id') == $wil->id ? 'selected' : '' }}>
                                    {{ $wil->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Berat Badan Saat Ini (kg)</label>
                        <input type="number" step="0.1" name="berat_badan" class="form-control"
                            value="{{ old('berat_badan') }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat') }}</textarea>
                </div>

                <button type="submit" class="btn w-100 py-2 fw-bold text-white mb-3"
                    style="background-color: #2a9d8f;">
                    Daftar Sekarang <i class="fa-solid fa-user-plus ms-1"></i>
                </button>

                <div class="text-center">
                    <span class="text-muted small">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #1d3557;">Masuk
                        di sini</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
