@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold text-dark">Kelola Video Edukasi</h4>
        <p class="text-muted small mb-0">Video yang diaktifkan akan tampil di Dashboard Warga</p>
    </div>
    <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Tambah Video Baru
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="table-card-wrapper shadow-sm bg-white rounded overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px">#</th>
                    <th style="width:100px">Thumbnail</th>
                    <th>Judul Video</th>
                    <th style="width:110px" class="text-center">Poin Reward</th>
                    <th style="width:90px" class="text-center">Urutan</th>
                    <th style="width:90px" class="text-center">Status</th>
                    <th style="width:130px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($videos as $video)
                <tr>
                    <td class="text-muted small">{{ $videos->firstItem() + $loop->index }}</td>
                    <td>
                        <a href="https://www.youtube.com/watch?v={{ $video->youtube_id }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ $video->thumbnail_url }}"
                                 alt="{{ e($video->title) }}"
                                 class="rounded"
                                 style="width:80px;height:45px;object-fit:cover;"
                                 loading="lazy">
                        </a>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $video->title }}</div>
                        @if($video->description)
                            <div class="text-muted small text-truncate" style="max-width:300px;">{{ $video->description }}</div>
                        @endif
                        <code class="small text-muted">{{ $video->youtube_id }}</code>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success fs-6">+{{ $video->points_reward }}</span>
                    </td>
                    <td class="text-center text-muted small">{{ $video->sort_order }}</td>
                    <td class="text-center">
                        @if($video->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus video \"{{ addslashes($video->title) }}\"? Semua data klaim warga terkait juga akan terhapus.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="fa-solid fa-film fa-2x mb-2 d-block opacity-25"></i>
                        Belum ada video. <a href="{{ route('admin.videos.create') }}">Tambah sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($videos->hasPages())
        <div class="p-3 border-top">
            {{ $videos->links() }}
        </div>
    @endif
</div>
@endsection
