@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">Tambah Video Edukasi</h4>
        <p class="text-muted small mb-0">Video akan ditampilkan di Dashboard Warga setelah diaktifkan</p>
    </div>
    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    {{-- Form Kiri --}}
    <div class="col-lg-7">
        <div class="table-card-wrapper shadow-sm bg-white p-4 rounded">
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong><i class="fa-solid fa-circle-exclamation me-1"></i>Periksa input Anda:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.videos.store') }}" method="POST" id="videoForm">
                @csrf

                {{-- YouTube ID / URL --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">YouTube ID atau URL <span class="text-danger">*</span></label>
                    <input type="text"
                           name="youtube_id"
                           id="youtube_id_input"
                           class="form-control @error('youtube_id') is-invalid @enderror"
                           value="{{ old('youtube_id') }}"
                           placeholder="Contoh: dQw4w9WgXcQ atau https://youtu.be/dQw4w9WgXcQ"
                           required>
                    @error('youtube_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Tempel URL YouTube atau ID-nya langsung (11 karakter).</div>
                </div>

                {{-- Judul --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Video <span class="text-danger">*</span></label>
                    <input type="text"
                           name="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}"
                           maxlength="255"
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi Singkat <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="3"
                              maxlength="1000"
                              placeholder="Ringkasan isi video untuk membantu warga memilih...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    {{-- Poin Reward --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Poin Reward <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-leaf text-success"></i></span>
                            <input type="number"
                                   name="points_reward"
                                   class="form-control @error('points_reward') is-invalid @enderror"
                                   value="{{ old('points_reward', 1) }}"
                                   min="1" max="100" required>
                        </div>
                        @error('points_reward')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Urutan Tampil --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Urutan Tampil</label>
                        <input type="number"
                               name="sort_order"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', 0) }}"
                               min="0" max="9999">
                        <div class="form-text">0 = paling atas</div>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 d-flex align-items-end pb-1">
                        <div class="form-check form-switch ms-1">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Aktifkan Video</label>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i> Simpan Video
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview Kanan --}}
    <div class="col-lg-5">
        <div class="table-card-wrapper shadow-sm bg-white p-4 rounded">
            <h6 class="fw-bold mb-3"><i class="fa-brands fa-youtube text-danger me-1"></i> Preview Thumbnail</h6>
            <div id="preview-wrapper" class="text-center text-muted py-4 border rounded bg-light">
                <i class="fa-solid fa-photo-film fa-2x mb-2 opacity-25"></i>
                <p class="small">Masukkan YouTube ID / URL untuk melihat preview</p>
            </div>
        </div>

        <div class="table-card-wrapper shadow-sm bg-white p-4 rounded mt-3">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-info text-primary me-1"></i> Cara Mendapatkan YouTube ID</h6>
            <ol class="small text-muted ps-3 mb-0">
                <li>Buka video di YouTube</li>
                <li>Salin URL dari address bar browser</li>
                <li>Tempel langsung di kolom di atas — sistem otomatis mengekstrak ID-nya</li>
            </ol>
            <div class="mt-2 p-2 bg-light rounded small font-monospace">
                youtu.be/<strong class="text-danger">dQw4w9WgXcQ</strong><br>
                youtube.com/watch?v=<strong class="text-danger">dQw4w9WgXcQ</strong>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ csp_nonce() }}">
document.getElementById('youtube_id_input').addEventListener('input', function () {
    var val = this.value.trim();
    // Ekstrak ID dari URL jika perlu
    var match = val.match(/(?:youtu\.be\/|v=|embed\/)([a-zA-Z0-9_\-]{11})/);
    var id = match ? match[1] : (val.length === 11 ? val : null);

    var wrapper = document.getElementById('preview-wrapper');
    if (id) {
        wrapper.innerHTML = '<img src="https://img.youtube.com/vi/' + id + '/hqdefault.jpg" class="img-fluid rounded" style="max-height:180px;object-fit:cover;" alt="Thumbnail">';
    } else {
        wrapper.innerHTML = '<i class="fa-solid fa-photo-film fa-2x mb-2 opacity-25 d-block"></i><p class="small">Masukkan YouTube ID / URL untuk melihat preview</p>';
    }
});
</script>
@endsection
