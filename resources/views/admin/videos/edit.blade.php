@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">Edit Video Edukasi</h4>
        <p class="text-muted small mb-0">Perubahan akan langsung terlihat di Dashboard Warga</p>
    </div>
    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
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

            <form action="{{ route('admin.videos.update', $video) }}" method="POST" id="videoForm">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">YouTube ID atau URL <span class="text-danger">*</span></label>
                    <input type="text"
                           name="youtube_id"
                           id="youtube_id_input"
                           class="form-control @error('youtube_id') is-invalid @enderror"
                           value="{{ old('youtube_id', $video->youtube_id) }}"
                           required>
                    @error('youtube_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Video <span class="text-danger">*</span></label>
                    <input type="text"
                           name="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $video->title) }}"
                           maxlength="255" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi Singkat <span class="text-muted fw-normal">(opsional)</span></label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="3" maxlength="1000">{{ old('description', $video->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Poin Reward <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-leaf text-success"></i></span>
                            <input type="number"
                                   name="points_reward"
                                   class="form-control @error('points_reward') is-invalid @enderror"
                                   value="{{ old('points_reward', $video->points_reward) }}"
                                   min="1" max="100" required>
                        </div>
                        @error('points_reward')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Urutan Tampil</label>
                        <input type="number"
                               name="sort_order"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $video->sort_order) }}"
                               min="0" max="9999">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 d-flex align-items-end pb-1">
                        <div class="form-check form-switch ms-1">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_active">Aktifkan Video</label>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.videos.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview --}}
    <div class="col-lg-5">
        <div class="table-card-wrapper shadow-sm bg-white p-4 rounded">
            <h6 class="fw-bold mb-3"><i class="fa-brands fa-youtube text-danger me-1"></i> Preview Saat Ini</h6>
            <div id="preview-wrapper" class="text-center">
                <img id="thumb-img"
                     src="{{ $video->thumbnail_url }}"
                     class="img-fluid rounded mb-2"
                     style="max-height:180px;object-fit:cover;"
                     alt="Thumbnail">
                <p class="small text-muted">
                    <i class="fa-solid fa-link me-1"></i>
                    <a href="https://www.youtube.com/watch?v={{ $video->youtube_id }}" target="_blank" rel="noopener noreferrer" class="text-muted">
                        Tonton di YouTube
                    </a>
                </p>
            </div>
        </div>

        <div class="table-card-wrapper shadow-sm bg-white p-3 rounded mt-3">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <div class="small text-muted">Total Klaim Warga</div>
                    <div class="fw-bold fs-4">{{ $video->claims()->count() }}</div>
                </div>
                <div class="vr"></div>
                <div>
                    <div class="small text-muted">Dibuat</div>
                    <div class="small">{{ $video->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ csp_nonce() }}">
document.getElementById('youtube_id_input').addEventListener('input', function () {
    var val = this.value.trim();
    var match = val.match(/(?:youtu\.be\/|v=|embed\/)([a-zA-Z0-9_\-]{11})/);
    var id = match ? match[1] : (val.length === 11 ? val : null);
    if (id) {
        document.getElementById('thumb-img').src = 'https://img.youtube.com/vi/' + id + '/hqdefault.jpg';
    }
});
</script>
@endsection
