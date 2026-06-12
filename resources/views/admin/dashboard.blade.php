@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap border-bottom pb-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Pusat Analitik GARDA 5</h2>
            <p class="text-muted mb-0 small">Fakultas Kesehatan Masyarakat · Universitas Muhammadiyah Purwokerto</p>
        </div>
        <div class="mt-2 mt-md-0 d-flex gap-2 align-items-center flex-wrap">
            <span class="badge bg-success p-2 fs-7"><i class="fa-solid fa-circle-dot me-1"></i>Sistem Aktif</span>
            <span class="badge bg-dark p-2 fs-7">Admin: <strong class="text-warning">{{ auth()->user()->nama_lengkap }}</strong></span>
            <span class="badge bg-secondary p-2 fs-7"><i class="fa-solid fa-clock me-1"></i>{{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
        </div>
    </div>

    {{-- ── KPI Row 1: Users ────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        {{-- Total Warga --}}
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 kpi-card" style="border-top:3px solid #4e73df!important;">
                <div class="card-body text-center py-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#eef2ff;">
                        <i class="fa-solid fa-users text-primary fs-5"></i>
                    </div>
                    <div class="fw-bold fs-3 text-primary lh-1 mb-1">{{ number_format($kpi['total_warga']) }}</div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Total Warga</div>
                </div>
            </div>
        </div>
        {{-- Active Warga --}}
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 kpi-card" style="border-top:3px solid #1cc88a!important;">
                <div class="card-body text-center py-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#d1fae5;">
                        <i class="fa-solid fa-user-check text-success fs-5"></i>
                    </div>
                    <div class="fw-bold fs-3 text-success lh-1 mb-1">{{ number_format($kpi['active_warga']) }}</div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Aktif 30 Hari</div>
                </div>
            </div>
        </div>
        {{-- New this month --}}
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 kpi-card" style="border-top:3px solid #36b9cc!important;">
                <div class="card-body text-center py-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#dbeafe;">
                        <i class="fa-solid fa-user-plus text-info fs-5"></i>
                    </div>
                    <div class="fw-bold fs-3 text-info lh-1 mb-1">{{ number_format($kpi['new_warga_this_month']) }}</div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Baru Bulan Ini</div>
                </div>
            </div>
        </div>
        {{-- Total Logs --}}
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 kpi-card" style="border-top:3px solid #6f42c1!important;">
                <div class="card-body text-center py-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#ede9fe;">
                        <i class="fa-solid fa-file-medical text-purple fs-5" style="color:#6f42c1;"></i>
                    </div>
                    <div class="fw-bold fs-3 lh-1 mb-1" style="color:#6f42c1;">{{ number_format($kpi['total_log']) }}</div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Total Log</div>
                </div>
            </div>
        </div>
        {{-- High Sodium --}}
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 kpi-card" style="border-top:3px solid #e74a3b!important;">
                <div class="card-body text-center py-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#fee2e2;">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                    </div>
                    <div class="fw-bold fs-3 text-danger lh-1 mb-1">{{ number_format($kpi['high_sodium']) }}</div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">High Sodium</div>
                </div>
            </div>
        </div>
        {{-- Avg Sistolik --}}
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 kpi-card" style="border-top:3px solid #f6c23e!important;">
                <div class="card-body text-center py-3">
                    <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#fef3c7;">
                        <i class="fa-solid fa-heart-pulse text-warning fs-5"></i>
                    </div>
                    <div class="fw-bold fs-3 text-warning lh-1 mb-1">{{ $kpi['avg_sistolik'] }}</div>
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.7rem;">Rerata Sistolik</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Quick Actions ────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-auto"><span class="fw-bold text-muted small text-uppercase">Aksi Cepat</span></div>
                <div class="col">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-user-plus me-1"></i>Tambah Pengguna
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-users me-1"></i>Semua Warga
                        </a>
                        <a href="{{ route('admin.videos.create') }}" class="btn btn-sm btn-outline-info">
                            <i class="fa-solid fa-video me-1"></i>Tambah Video
                        </a>
                        <a href="{{ route('admin.videos.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-film me-1"></i>Kelola Video
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Charts Row 1 ─────────────────────────────────────────────────────── --}}
    <div class="row g-4 mb-4">
        {{-- Trend chart --}}
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
        {{-- Gender doughnut --}}
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

    {{-- ── Charts Row 2 ─────────────────────────────────────────────────────── --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-bar text-danger me-2"></i>Profil Status Hipertensi</h6>
                </div>
                <div class="card-body"><div style="height:260px;"><canvas id="chartHipertensi"></canvas></div></div>
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

    {{-- ── Top Active + Inactive Users ─────────────────────────────────────── --}}
    <div class="row g-4 mb-4">
        {{-- Top active --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-trophy text-warning me-2"></i>Warga Paling Aktif Bulan Ini</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">#</th>
                                <th>Nama</th>
                                <th>Wilayah</th>
                                <th class="text-center">Log</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topActiveUsers as $u)
                            <tr>
                                <td class="px-3">
                                    @if($loop->index === 0) <span class="badge bg-warning text-dark">🥇</span>
                                    @elseif($loop->index === 1) <span class="badge bg-secondary">🥈</span>
                                    @elseif($loop->index === 2) <span class="badge bg-danger">🥉</span>
                                    @else {{ $loop->iteration }}
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $u['nama_lengkap'] }}</div>
                                    <div class="text-muted small">{{ $u['nik'] }}</div>
                                </td>
                                <td class="text-muted small">{{ $u['wilayah_name'] ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $u['log_count'] }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.detail', $u['id']) }}" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:11px;">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada data bulan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Inactive users alert --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-user-slash me-2"></i>Tidak Aktif 30 Hari</h6>
                    <span class="badge bg-danger">{{ count($inactiveUsers) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Nama</th>
                                <th>Wilayah</th>
                                <th>Bergabung</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inactiveUsers as $u)
                            <tr>
                                <td class="px-3">
                                    <div class="fw-semibold">{{ $u['nama_lengkap'] }}</div>
                                    <div class="text-muted small">{{ $u['nik'] }}</div>
                                </td>
                                <td class="text-muted small">{{ $u['wilayah_name'] ?? '—' }}</td>
                                <td class="text-muted small">{{ \Carbon\Carbon::parse($u['created_at'])->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.detail', $u['id']) }}" class="btn btn-xs btn-outline-warning py-0 px-2" style="font-size:11px;">Lihat</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted"><i class="fa-solid fa-circle-check text-success me-1"></i>Semua warga aktif!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent Health Logs ───────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-file-waveform text-success me-2"></i>Log Kesehatan Terbaru</h6>
            <span class="badge bg-light text-muted">8 Terkini</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="px-3">Waktu</th>
                        <th>Warga</th>
                        <th>Berat</th>
                        <th>Tensi</th>
                        <th>Garam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentHealthLogs as $log)
                    <tr>
                        <td class="px-3 text-muted small">{{ $log->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="fw-semibold">{{ $log->user?->nama_lengkap ?? '—' }}</div>
                            <div class="text-muted small">{{ $log->user?->nik }}</div>
                        </td>
                        <td class="text-muted small">{{ $log->berat_badan }} kg</td>
                        <td class="font-monospace small">{{ $log->tekanan_darah ?? '—' }}</td>
                        <td>
                            @if($log->konsumsi_garam === 'more')
                                <span class="badge bg-danger">Lebih</span>
                            @elseif($log->konsumsi_garam === 'less')
                                <span class="badge bg-info">Kurang</span>
                            @else
                                <span class="badge bg-success">Ideal</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusMap = ['Normal'=>'success','Ringan'=>'warning','Sedang'=>'orange','Berat'=>'danger'];
                                $color = $statusMap[$log->status_hipertensi] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ $log->status_hipertensi }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-3 text-muted">Belum ada log kesehatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Audit Log ────────────────────────────────────────────────────────── --}}
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
                        <td class="fw-semibold">{{ $log->user?->nama_lengkap ?? 'Sistem' }}</td>
                        <td><span class="badge bg-secondary">{{ $log->user?->role ?? '-' }}</span></td>
                        <td>
                            <span class="badge bg-primary me-1">{{ $log->action }}</span>
                            <small class="text-muted">{{ Str::limit($log->description, 60) }}</small>
                        </td>
                        <td class="px-3 text-muted small font-monospace">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada rekam jejak.</td></tr>
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
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, ticks:{stepSize:1}}} }
});

new Chart(document.getElementById('chartGender'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($genderStats)) !!},
        datasets: [{ data: {!! json_encode(array_values($genderStats)) !!}, backgroundColor: ['#4e73df','#e84393','#36b9cc'], borderWidth:2 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
});

new Chart(document.getElementById('chartHipertensi'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($hipertensiStats)) !!},
        datasets: [{ label:'Jumlah', data: {!! json_encode(array_values($hipertensiStats)) !!}, backgroundColor:['#1cc88a','#f6c23e','#fd7e14','#e74a3b'], borderRadius:6 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});

new Chart(document.getElementById('chartWilayah'), {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_column($wilayahStats, 'nama_wilayah')) !!},
        datasets: [{ data: {!! json_encode(array_column($wilayahStats, 'users_count')) !!}, backgroundColor:['#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796'], borderWidth:2 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
});
</script>
@endsection