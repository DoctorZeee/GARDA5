@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark fw-bold">Tambah Pengguna Baru</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="table-card-wrapper shadow-sm bg-white p-4 rounded">
    @if($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fa-solid fa-circle-exclamation me-1"></i>Ada kesalahan pada input:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off">
        @csrf

        {{-- Baris 1: NIK + Nama --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">NIK <span class="text-danger">*</span> <small class="fw-normal text-muted">(16 Digit)</small></label>
                <input type="text"
                       name="nik"
                       class="form-control @error('nik') is-invalid @enderror"
                       value="{{ old('nik') }}"
                       maxlength="16"
                       pattern="[0-9]{16}"
                       required>
                @error('nik')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
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

        {{-- Baris 2: Email + Role --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Role Akses <span class="text-danger">*</span></label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="user"      {{ old('role') === 'user'      ? 'selected' : '' }}>User (Masyarakat)</option>
                    <option value="kader"     {{ old('role') === 'kader'     ? 'selected' : '' }}>Kader Posyandu</option>
                    <option value="puskesmas" {{ old('role') === 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                    <option value="admin"     {{ old('role') === 'admin'     ? 'selected' : '' }}>Admin (Pengembang)</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- PERBAIKAN: Baris 3: Password + Confirm Password --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           minlength="8"
                           required
                           autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)" title="Tampilkan/sembunyikan">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text">Minimal 8 karakter.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           required
                           autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)" title="Tampilkan/sembunyikan">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text text-muted" id="pw-match-hint">&nbsp;</div>
            </div>
        </div>

        {{-- Baris 4: Tempat Lahir + Tanggal Lahir + Jenis Kelamin --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
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
                <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date"
                       name="tanggal_lahir"
                       class="form-control @error('tanggal_lahir') is-invalid @enderror"
                       value="{{ old('tanggal_lahir') }}"
                       max="{{ date('Y-m-d') }}"
                       required>
                @error('tanggal_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
                    <option value="L" {{ old('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Baris 5: Wilayah + Berat Badan + Tekanan Darah --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Wilayah (Posyandu)</label>
                <select name="wilayah_id" class="form-select @error('wilayah_id') is-invalid @enderror">
                    <option value="">-- Kosongkan jika Admin/Puskesmas --</option>
                    @foreach($wilayahs as $wil)
                        <option value="{{ $wil->id }}" {{ old('wilayah_id') == $wil->id ? 'selected' : '' }}>
                            {{ $wil->nama_wilayah }}
                        </option>
                    @endforeach
                </select>
                @error('wilayah_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Berat Badan (kg) <span class="text-danger">*</span></label>
                <input type="number"
                       step="0.1"
                       name="berat_badan"
                       class="form-control @error('berat_badan') is-invalid @enderror"
                       value="{{ old('berat_badan') }}"
                       min="10" max="300"
                       required>
                @error('berat_badan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Tekanan Darah <small class="text-muted fw-normal">(Opsional, cth: 120/80)</small></label>
                <input type="text"
                       name="tekanan_darah"
                       class="form-control @error('tekanan_darah') is-invalid @enderror"
                       value="{{ old('tekanan_darah') }}"
                       placeholder="120/80">
                @error('tekanan_darah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Alamat --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="alamat"
                      class="form-control @error('alamat') is-invalid @enderror"
                      rows="3"
                      required>{{ old('alamat') }}</textarea>
            @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save me-1"></i> Simpan Data Pengguna
            </button>
        </div>
    </form>
</div>

<script nonce="{{ csp_nonce() }}">
function togglePassword(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Real-time password match indicator
var pwInput    = document.getElementById('password');
var pwConfirm  = document.getElementById('password_confirmation');
var hint       = document.getElementById('pw-match-hint');

function checkMatch() {
    if (!pwConfirm.value) { hint.textContent = ''; hint.className = 'form-text'; return; }
    if (pwInput.value === pwConfirm.value) {
        hint.textContent = '✓ Password cocok';
        hint.className   = 'form-text text-success';
    } else {
        hint.textContent = '✗ Password tidak cocok';
        hint.className   = 'form-text text-danger';
    }
}

pwInput.addEventListener('input', checkMatch);
pwConfirm.addEventListener('input', checkMatch);
</script>
@endsection
