@extends('layouts.kader')

@section('content')

{{-- Stat Cards --}}
<div class="metrics-summary-row-grid mb-4">
    <div class="kader-stat-card kader-stat-green">
        <div class="stat-num">{{ $wargaLogs->total() }}</div>
        <div class="stat-lbl">Total Log Masuk</div>
    </div>
    <div class="kader-stat-card kader-stat-ok">
        <div class="stat-num">{{ $wargaLogs->where('status_hipertensi','Normal')->count() }}</div>
        <div class="stat-lbl">Status Normal</div>
    </div>
    <div class="kader-stat-card kader-stat-warn">
        <div class="stat-num">{{ $wargaLogs->whereIn('status_hipertensi',['Ringan','Sedang'])->count() }}</div>
        <div class="stat-lbl">Perlu Perhatian</div>
    </div>
    <div class="kader-stat-card kader-stat-danger">
        <div class="stat-num">{{ $wargaLogs->where('status_hipertensi','Berat')->count() }}</div>
        <div class="stat-lbl">Hipertensi Berat</div>
    </div>
</div>

{{-- Tabel --}}
<div class="table-card-wrapper shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h6 class="fw-bold mb-0">
            <i class="fa-solid fa-table-list me-2 text-success"></i>Log Kesehatan Warga Binaan
        </h6>
        <div class="d-flex gap-2 align-items-center">
            <label class="small fw-semibold text-muted mb-0">Filter Status:</label>
            <select id="filterStatus" class="filter-select-engine">
                <option value="all">Semua</option>
                <option value="Normal">Normal</option>
                <option value="Ringan">Ringan</option>
                <option value="Sedang">Sedang</option>
                <option value="Berat">Berat</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="px-3">Tanggal</th>
                    <th>Nama Warga</th>
                    <th>Tekanan Darah</th>
                    <th>BB (kg)</th>
                    <th>TB (cm)</th>
                    <th>Garam</th>
                    <th>Status</th>
                    <th>Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wargaLogs as $log)
                <tr data-status="{{ $log->status_hipertensi }}">
                    <td class="px-3 text-muted small">{{ $log->tanggal_input->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $log->user->nama_lengkap ?? '-' }}</td>
                    <td>
                        @if($log->tekanan_darah)
                            <span class="font-monospace">{{ $log->tekanan_darah }}</span>
                            <small class="text-muted">mmHg</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $log->berat_badan }}</td>
                    <td>{{ $log->tinggi_badan }}</td>
                    <td>
                        @php
                            [$garamLabel, $garamClass] = match($log->konsumsi_garam) {
                                'less'  => ['< 5g', 'normal-pill'],
                                'ideal' => ['5g',   'ringan-pill'],
                                'more'  => ['> 5g', 'berat-pill'],
                                default => ['-',    ''],
                            };
                        @endphp
                        <span class="pill-status {{ $garamClass }}">{{ $garamLabel }}</span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($log->status_hipertensi) {
                                'Normal' => 'normal-pill',
                                'Ringan' => 'ringan-pill',
                                'Sedang' => 'sedang-pill',
                                'Berat'  => 'berat-pill',
                                default  => '',
                            };
                        @endphp
                        <span class="pill-status {{ $statusClass }}">{{ $log->status_hipertensi }}</span>
                    </td>
                    <td>
                        <span class="pill-status" style="background: #f1f5f9; color: #334155;">
                            🌿 {{ $log->user->point->total_points ?? 0 }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>
                        Belum ada data log di wilayah Anda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($wargaLogs->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <div class="text-muted small">
            Menampilkan {{ $wargaLogs->firstItem() }}–{{ $wargaLogs->lastItem() }} dari {{ $wargaLogs->total() }} entri
        </div>
        {{ $wargaLogs->links() }}
    </div>
    @endif
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#filterStatus').on('change', function () {
        const val = $(this).val();
        $('tbody tr[data-status]').each(function () {
            $(this).toggle(val === 'all' || $(this).data('status') === val);
        });
    });
});
</script>
@endsection
