@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark font-weight-bold">Tambah Pengguna Baru</h4>
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

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">NIK (16 Digit)</label>
                <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Role Akses</label>
                <select name="role" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Masyarakat)</option>
                    <option value="kader" {{ old('role') == 'kader' ? 'selected' : '' }}>Kader Posyandu</option>
                    <option value="puskesmas" {{ old('role') == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Pengembang)</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Wilayah (Posyandu)</label>
                <select name="wilayah_id" class="form-select">
                    <option value="">-- Kosongkan jika Admin/Puskesmas --</option>
                    @foreach($wilayahs as $wil)
                        <option value="{{ $wil->id }}" {{ old('wilayah_id') == $wil->id ? 'selected' : '' }}>{{ $wil->nama_wilayah }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Berat Badan (kg)</label>
                <input type="number" step="0.1" name="berat_badan" class="form-control" value="{{ old('berat_badan') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Tekanan Darah <small class="text-muted">(Opsional, cth: 120/80)</small></label>
                <input type="text" name="tekanan_darah" class="form-control" value="{{ old('tekanan_darah') }}" placeholder="120/80">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Alamat Lengkap</label>
            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Data Pengguna</button>
        </div>
    </form>
</div>
@endsection