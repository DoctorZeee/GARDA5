@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark font-weight-bold">
        <i class="fa-solid fa-users text-primary"></i> Daftar Pengguna
    </h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Pengguna
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="table-card-wrapper shadow-sm bg-white p-3 rounded">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Wilayah</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td><code>{{ $user->nik }}</code></td>
                    <td class="fw-bold">{{ $user->nama_lengkap }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $badgeColor = match($user->role) {
                                'admin'     => 'bg-danger',
                                'puskesmas' => 'bg-info text-dark',
                                'kader'     => 'bg-warning text-dark',
                                default     => 'bg-primary',
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td>{{ $user->wilayah->nama_wilayah ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning me-1" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus pengguna {{ $user->nama_lengkap }}? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada pengguna terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
