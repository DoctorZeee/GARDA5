@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-dark fw-bold">
        <i class="fa-solid fa-users text-primary"></i> Daftar Pengguna
    </h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Pengguna
    </a>
</div>

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
                <tr @if($user->trashed()) class="table-secondary opacity-75" @endif>
                    <td><code>{{ $user->nik }}</code></td>
                    <td class="fw-bold">
                        {{ $user->nama_lengkap }}
                        @if($user->trashed())
                            <span class="badge bg-secondary ms-1">Dihapus</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $badgeColor = match($user->role) {
                                'admin'     => 'bg-danger',
                                'puskesmas' => 'bg-info text-dark',
                                'kader'     => 'bg-warning text-dark',
                                default     => 'bg-primary',
                            };
                            $roleLabel = \App\Enums\UserRole::tryFrom($user->role)?->label() ?? ucfirst($user->role);
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ $roleLabel }}</span>
                    </td>
                    <td>{{ $user->wilayah->nama_wilayah ?? '-' }}</td>
                    <td class="text-center">
                        @if($user->trashed())
                            {{-- Restore soft-deleted user --}}
                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Pulihkan">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="btn btn-sm btn-warning me-1" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            @can('delete', $user)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus pengguna {{ addslashes($user->nama_lengkap) }}? Data dapat dipulihkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fa-solid fa-inbox me-2"></i>Belum ada pengguna terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
