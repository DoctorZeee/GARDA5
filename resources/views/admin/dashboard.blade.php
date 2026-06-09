@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap border-bottom pb-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Pusat Analitik GARDA 5</h2>
            <p class="text-muted mb-0 small">Fakultas Kesehatan Masyarakat · Universitas Muhammadiyah Purwokerto</p>
        </div>
        <div class="mt-2 mt-md-0 d-flex gap-2 align-items-center">
            <span class="badge bg-success p-2 fs-7"><i class="fa-solid fa-circle-dot me-1"></i>Sistem Aktif</span>
            <span class="badge bg-dark p-2 fs-7">Admin: <strong class="text-warning">{{ auth()->user()->nama_lengkap }}</strong></span>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #4e73df !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#eef2ff;">
                        <i class="fa-solid fa-users text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Warga Binaan</div>
                        <div class="fw-bold fs-3 text-primary lh-1">{{ number_format($kpi['total_warga']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #1cc88a !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#d1fae5;">
                        <i class="fa-solid fa-file-medical text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Log</div>
                        <div class="fw-bold fs-3 text-success lh-1">{{ number_format($kpi['total_log']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #e74a3b !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#fee2e2;">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">High Sodium</div>
                        <div class="fw-bold fs-3 text-danger lh-1">{{ number_format($kpi['high_sodium']) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #f6c23e !important;">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#fef3c7;">
                        <i class="fa-solid fa-heart-pulse text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Rerata Sistolik</div>
                        <div class="fw-bold fs-3 text-warning lh-1">{{ $kpi['avg_sistolik'] }} <span class="fs-6 text-muted fw-normal">mmHg</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i>Tren Keaktifan Warga</h6>
                    <span class="badge bg-light text-muted">7 Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <div style="height:280px;"><canvas id="chartTren"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-venus-mars text-info me-2"></i>Komposisi Gender</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="height:240px;width:100%;"><canvas id="chartGender"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-bar text-danger me-2"></i>Profil Status Hipertensi</h6>
                </div>
                <div class="card-body">
                    <div style="height:260px;"><canvas id="chartHipertensi"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-map-location-dot text-success me-2"></i>Sebaran per Wilayah</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="height:260px;width:100%;"><canvas id="chartWilayah"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Audit Log --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-2">
        <div class="card-header d-flex justify-content-between align-items-center py-3" style="background:#0f172a;">
            <h6 class="fw-bold mb-0 text-white"><i class="fa-solid fa-terminal me-2 text-green-400"></i>Live Audit Trail</h6>
            <span class="badge bg-success"><i class="fa-solid fa-circle me-1" style="font-size:8px;"></i>Live</span>
        </div>
        <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="px-3">Waktu</th>
                        <th>Aktor</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                        <th class="px-3">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                    <tr>
                        <td class="px-3 font-monospace text-muted small">{{ $log->created_at->format('d/m H:i:s') }}</td>
                        <td class="fw-semibold">{{ $log->user->nama_lengkap ?? 'Sistem' }}</td>
                        <td><span class="badge bg-secondary">{{ $log->user->role ?? '-' }}</span></td>
                        <td>
                            <span class="badge bg-primary me-1">{{ $log->action }}</span>
                            <small class="text-muted">{{ Str::limit($log->description, 60) }}</small>
                        </td>
                        <td class="px-3 text-muted small font-monospace">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada rekam jejak aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">
const trenLabels = {!! json_encode(array_column($trenKesehatan, 'date')) !!};
const trenData   = {!! json_encode(array_column($trenKesehatan, 'count')) !!};

new Chart(document.getElementById('chartTren'), {
    type: 'line',
    data: {
        labels: trenLabels,
        datasets: [{ label: 'Log Masuk', data: trenData, borderColor: '#4e73df', backgroundColor: 'rgba(78,115,223,0.08)', tension: 0.4, fill: true, pointRadius: 4 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

new Chart(document.getElementById('chartGender'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($genderStats)) !!},
        datasets: [{ data: {!! json_encode(array_values($genderStats)) !!}, backgroundColor: ['#4e73df','#e84393','#36b9cc'], borderWidth: 2 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('chartHipertensi'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($hipertensiStats)) !!},
        datasets: [{ label: 'Jumlah', data: {!! json_encode(array_values($hipertensiStats)) !!}, backgroundColor: ['#1cc88a','#f6c23e','#fd7e14','#e74a3b'], borderRadius: 6 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('chartWilayah'), {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_column($wilayahStats, 'nama_wilayah')) !!},
        datasets: [{ data: {!! json_encode(array_column($wilayahStats, 'users_count')) !!}, backgroundColor: ['#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'], borderWidth: 2 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection
