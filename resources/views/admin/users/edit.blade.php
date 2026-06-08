@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark font-weight-bold">Edit Data Pengguna</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>

<div class="table-card-wrapper shadow-sm bg-white p-4 rounded">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT') <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">NIK (16 Digit)</label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik', $user->nik) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Password Baru <small class="text-danger">(Kosongkan jika tidak diubah)</small></label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Role Akses</label>
                <select name="role" class="form-select" required>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User (Masyarakat)</option>
                    <option value="kader" {{ old('role', $user->role) == 'kader' ? 'selected' : '' }}>Kader Posyandu</option>
                    <option value="puskesmas" {{ old('role', $user->role) == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Pengembang)</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $user->tanggal_lahir->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Wilayah (Posyandu)</label>
                <select name="wilayah_id" class="form-select">
                    <option value="">-- Kosongkan jika Admin/Puskesmas --</option>
                    @foreach($wilayahs as $wil)
                        <option value="{{ $wil->id }}" {{ old('wilayah_id', $user->wilayah_id) == $wil->id ? 'selected' : '' }}>{{ $wil->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Berat Badan (kg)</label>
                <input type="number" step="0.1" name="berat_badan" class="form-control" value="{{ old('berat_badan', $user->berat_badan) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Tekanan Darah <small class="text-muted">(Opsional)</small></label>
                <input type="text" name="tekanan_darah" class="form-control" value="{{ old('tekanan_darah', $user->tekanan_darah) }}" placeholder="120/80">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Alamat Lengkap</label>
            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $user->alamat) }}</textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-warning"><i class="fa-solid fa-save"></i> Perbarui Data Pengguna</button>
        </div>
    </form>
</div>
@endsection