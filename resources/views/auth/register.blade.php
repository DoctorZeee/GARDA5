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
        <div class="login-card shadow border-0" style="width: 100%; max-width: 720px; padding: 40px;">

            {{-- Header --}}
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark">Daftar Akun Warga</h3>
                <p class="text-muted small">Bergabunglah dengan GARDA 5 untuk memantau kesehatan Anda.</p>
            </div>

            {{-- Error Summary --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" autocomplete="off">
                @csrf

                {{-- Identitas --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">NIK <span class="text-danger">*</span> <span class="fw-normal text-muted">(16 Digit)</span></label>
                        <input type="text"
                               name="nik"
                               class="form-control @error('nik') is-invalid @enderror"
                               value="{{ old('nik') }}"
                               maxlength="16"
                               pattern="[0-9]{16}"
                               placeholder="16 digit angka NIK"
                               required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text"
                               name="nama_lengkap"
                               class="form-control @error('nama_lengkap') is-invalid @enderror"
                               value="{{ old('nama_lengkap') }}"
                               required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label fw-bold small">Email Aktif <span class="text-danger">*</span></label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="contoh@email.com"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PERBAIKAN: Password + Konfirmasi Sejajar --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Kata Sandi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password"
                                   name="password"
                                   id="reg_password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   minlength="8"
                                   placeholder="Min. 8 karakter"
                                   required
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePw('reg_password', this)"
                                    title="Tampilkan/sembunyikan kata sandi">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password"
                                   name="password_confirmation"
                                   id="reg_password_confirmation"
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Ulangi kata sandi"
                                   required
                                   autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="togglePw('reg_password_confirmation', this)"
                                    title="Tampilkan/sembunyikan konfirmasi">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text" id="reg-pw-hint">&nbsp;</div>
                    </div>
                </div>

                {{-- Data Diri --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text"
                               name="tempat_lahir"
                               class="form-control @error('tempat_lahir') is-invalid @enderror"
                               value="{{ old('tempat_lahir') }}"
                               required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date"
                               name="tanggal_lahir"
                               class="form-control @error('tanggal_lahir') is-invalid @enderror"
                               max="{{ date('Y-m-d') }}"
                               value="{{ old('tanggal_lahir') }}"
                               required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                            <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Wilayah + Berat Badan --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Wilayah (Area Observasi) <span class="text-danger">*</span></label>
                        <select name="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Wilayah Anda --</option>
                            @foreach ($wilayahs as $wil)
                                <option value="{{ $wil->id }}" {{ old('wilayah_id') == $wil->id ? 'selected' : '' }}>
                                    {{ $wil->nama_wilayah }}
                                </option>
                            @endforeach
                        </select>
                        @error('wilayah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Berat Badan Saat Ini (kg) <span class="text-danger">*</span></label>
                        <input type="number"
                               step="0.1"
                               name="berat_badan"
                               class="form-control @error('berat_badan') is-invalid @enderror"
                               value="{{ old('berat_badan') }}"
                               min="10" max="300"
                               placeholder="cth: 65"
                               required>
                        @error('berat_badan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="mb-4">
                    <label class="form-label fw-bold small">Alamat Lengkap <span class="text-danger">*</span></label>
                    <textarea name="alamat"
                              class="form-control @error('alamat') is-invalid @enderror"
                              rows="2"
                              required>{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn w-100 py-2 fw-bold text-white mb-3"
                        style="background-color: #2a9d8f;">
                    Daftar Sekarang <i class="fa-solid fa-user-plus ms-1"></i>
                </button>

                <div class="text-center">
                    <span class="text-muted small">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #1d3557;">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script nonce="{{ csp_nonce() ?? '' }}">
    function togglePw(fieldId, btn) {
        var input = document.getElementById(fieldId);
        var icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Real-time password match check
    var pw  = document.getElementById('reg_password');
    var pwc = document.getElementById('reg_password_confirmation');
    var hint = document.getElementById('reg-pw-hint');

    function checkMatch() {
        if (!pwc.value) { hint.textContent = ''; return; }
        if (pw.value === pwc.value) {
            hint.textContent  = '✓ Kata sandi cocok';
            hint.style.color  = '#198754';
        } else {
            hint.textContent  = '✗ Kata sandi tidak cocok';
            hint.style.color  = '#dc3545';
        }
    }
    pw.addEventListener('input', checkMatch);
    pwc.addEventListener('input', checkMatch);
    </script>
</body>
</html>
