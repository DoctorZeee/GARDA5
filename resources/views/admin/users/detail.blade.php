@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- ── Breadcrumb ────────────────────────────────────────────────────────── --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
            <li class="breadcrumb-item active">{{ $user->nama_lengkap }}</li>
        </ol>
    </nav>

    {{-- ── Anomaly Alerts ───────────────────────────────────────────────────── --}}
    @foreach($anomalies as $a)
    <div class="alert alert-{{ $a['type'] }} d-flex align-items-center gap-2 py-2 mb-3" role="alert">
        <i class="fa-solid {{ $a['icon'] }} shrink-0"></i>
        <span class="small">{{ $a['message'] }}</span>
    </div>
    @endforeach

    {{-- ── Profile Card ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <div class="row g-4 align-items-start">
                {{-- Left: Avatar + name --}}
                <div class="col-12 col-md-auto text-center text-md-start">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto mx-md-0"
                         style="width:72px;height:72px;font-size:28px;color:white;font-weight:bold;">
                        {{ mb_substr($user->nama_lengkap, 0, 1) }}
                    </div>
                </div>
                {{-- Middle: Personal info --}}
                <div class="col-12 col-md">
                    <h4 class="fw-bold mb-1">{{ $user->nama_lengkap }}</h4>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-primary">{{ $user->roleLabel() }}</span>
                        <span class="badge bg-light text-dark">NIK: {{ $user->nik }}</span>
                        @if($user->wilayah)
                        <span class="badge bg-light text-dark"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $user->wilayah->nama_wilayah }}</span>
                        @endif
                    </div>
                    <div class="row g-2 small text-muted">
                        <div class="col-6 col-lg-3"><i class="fa-solid fa-envelope me-1"></i>{{ $user->email }}</div>
                        <div class="col-6 col-lg-3"><i class="fa-solid fa-cake-candles me-1"></i>{{ $user->tanggal_lahir?->format('d M Y') ?? '—' }} ({{ $user->umur }} thn)</div>
                        <div class="col-6 col-lg-3"><i class="fa-solid fa-mars-and-venus me-1"></i>{{ $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        <div class="col-6 col-lg-3"><i class="fa-solid fa-weight-scale me-1"></i>{{ $user->berat_badan }} kg</div>
                        <div class="col-12"><i class="fa-solid fa-location-pin me-1"></i>{{ $user->alamat }}</div>
                        <div class="col-6 col-lg-3"><i class="fa-solid fa-calendar-plus me-1"></i>Bergabung {{ $user->created_at->format('d M Y') }}</div>
                        @if($user->tekanan_darah)
                        <div class="col-6 col-lg-3"><i class="fa-solid fa-heart-pulse me-1"></i>Tensi: {{ $user->tekanan_darah }}</div>
                        @endif
                    </div>
                </div>
                {{-- Right: Plant/Point summary --}}
                <div class="col-12 col-md-auto">
                    <div class="card bg-light border-0 rounded-3">
                        <div class="card-body text-center px-4 py-3">
                            <div class="fs-1">🌳</div>
                            <div class="fw-bold fs-4 text-success">{{ $user->point?->total_leaves ?? 0 }}</div>
                            <div class="small text-muted">Daun Aktif</div>
                            <hr class="my-2">
                            <div class="d-flex gap-3 justify-content-center small">
                                <div>
                                    <div class="fw-bold text-primary">{{ $user->point?->total_points ?? 0 }}</div>
                                    <div class="text-muted">Total Poin</div>
                                </div>
                                <div>
                                    <div class="fw-bold text-warning">{{ $user->point?->checkin_streak ?? 0 }}</div>
                                    <div class="text-muted">Streak</div>
                                </div>
                                <div>
                                    <div class="fw-bold text-info">{{ $user->point?->checkin_count ?? 0 }}</div>
                                    <div class="text-muted">Check-in</div>
                                </div>
                            </div>
                            @if($user->point?->last_checkin_date)
                            <div class="text-muted mt-2" style="font-size:11px;">
                                Last: {{ $user->point->last_checkin_date->format('d M Y') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- Action buttons --}}
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-pen me-1"></i>Edit Data
                </a>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHardReset">
                    <i class="fa-solid fa-rotate-left me-1"></i>Hard Reset Pohon
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- ── Charts Row ───────────────────────────────────────────────────────── --}}
    <div class="row g-4 mb-4">
        {{-- BP Trend --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-heart-pulse text-danger me-2"></i>Tren Tekanan Darah</h6>
                </div>
                <div class="card-body"><div style="height:240px;"><canvas id="chartBP"></canvas></div></div>
            </div>
        </div>
        {{-- Hypertension distribution --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-pie text-warning me-2"></i>Distribusi Hipertensi</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    @if($hyperDist->isEmpty())
                        <p class="text-muted">Belum ada data.</p>
                    @else
                        <div style="height:200px;width:100%;"><canvas id="chartHyper"></canvas></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Health Log History ───────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fa-solid fa-file-medical text-success me-2"></i>Riwayat Log Kesehatan</h6>
            <span class="badge bg-light text-muted">{{ $healthLogs->total() }} entri</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="px-3">Tanggal</th>
                        <th>Berat</th>
                        <th>Tinggi</th>
                        <th>Tensi</th>
                        <th>Garam</th>
                        <th>Hipertensi</th>
                        <th>Keluhan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($healthLogs as $log)
                    <tr>
                        <td class="px-3 fw-semibold small">{{ $log->tanggal_input->format('d M Y') }}</td>
                        <td class="small">{{ $log->berat_badan }} kg</td>
                        <td class="small">{{ $log->tinggi_badan }} cm</td>
                        <td class="font-monospace small">{{ $log->tekanan_darah ?? '—' }}</td>
                        <td>
                            @if($log->konsumsi_garam === 'more') <span class="badge bg-danger">Lebih</span>
                            @elseif($log->konsumsi_garam === 'less') <span class="badge bg-info">Kurang</span>
                            @else <span class="badge bg-success">Ideal</span>
                            @endif
                        </td>
                        <td>
                            @php $hColors = ['Normal'=>'success','Ringan'=>'warning','Sedang'=>'orange','Berat'=>'danger']; @endphp
                            <span class="badge bg-{{ $hColors[$log->status_hipertensi] ?? 'secondary' }}">{{ $log->status_hipertensi }}</span>
                        </td>
                        <td class="small text-muted">{{ Str::limit($log->keluhan ?? '—', 40) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada log kesehatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($healthLogs->hasPages())
        <div class="card-footer bg-white border-top py-2">
            {{ $healthLogs->links() }}
        </div>
        @endif
    </div>

    {{-- ── Reset History ────────────────────────────────────────────────────── --}}
    @if($resetHistory->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-clock-rotate-left me-2"></i>Riwayat Hard Reset</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="px-3">Waktu</th>
                        <th>Admin</th>
                        <th>Sebelum (Poin/Daun)</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resetHistory as $r)
                    <tr>
                        <td class="px-3 small text-muted">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y H:i') }}</td>
                        <td class="small fw-semibold">{{ $r->admin_name }} <span class="text-muted">({{ $r->admin_nik }})</span></td>
                        <td class="small">{{ $r->before_total_points }} poin / {{ $r->before_total_leaves }} daun</td>
                        <td class="small text-muted">{{ $r->reason ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ── Hard Reset Modal ──────────────────────────────────────────────────────── --}}
<div class="modal fade" id="modalHardReset" tabindex="-1" aria-labelledby="modalHardResetLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHardResetLabel">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Konfirmasi Hard Reset Pohon
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.hard-reset', $user) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>⚠ Perhatian!</strong> Tindakan ini akan mereset <strong>seluruh data pohon</strong> milik
                        <strong>{{ $user->nama_lengkap }}</strong> ({{ $user->nik }}) ke kondisi awal:
                        <ul class="mt-2 mb-0 small">
                            <li>Total Poin → 0</li>
                            <li>Total Daun → 0</li>
                            <li>Streak → 0</li>
                            <li>Check-in Count → 0</li>
                        </ul>
                        <p class="mt-2 mb-0 small">Data sebelum reset akan diarsipkan untuk keperluan audit.</p>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Alasan Reset (opsional)</label>
                        <textarea name="reason" id="reason" rows="2" class="form-control"
                            placeholder="Misal: Data tidak valid, permintaan supervisi, dll."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="confirm" id="confirmReset" value="1" required>
                        <label class="form-check-label fw-semibold text-danger" for="confirmReset">
                            Saya memahami risiko dan ingin melanjutkan hard reset.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-rotate-left me-1"></i>Ya, Reset Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">
// BP Trend Chart
const bpLabels   = {!! json_encode($bpTrend->pluck('tanggal_input')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!};
const systolicData = {!! json_encode($bpTrend->map(function($r){ $p=explode('/',$r->tekanan_darah,2); return isset($p[0])&&is_numeric($p[0])?(int)$p[0]:null; })) !!};
const diastolicData = {!! json_encode($bpTrend->map(function($r){ $p=explode('/',$r->tekanan_darah,2); return isset($p[1])&&is_numeric($p[1])?(int)$p[1]:null; })) !!};

if (bpLabels.length > 0) {
    new Chart(document.getElementById('chartBP'), {
        type: 'line',
        data: {
            labels: bpLabels,
            datasets: [
                { label: 'Sistolik', data: systolicData, borderColor:'#e74a3b', backgroundColor:'rgba(231,74,59,0.05)', tension:0.4, fill:true, pointRadius:3 },
                { label: 'Diastolik', data: diastolicData, borderColor:'#4e73df', backgroundColor:'rgba(78,115,223,0.05)', tension:0.4, fill:true, pointRadius:3 },
            ]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:false, min:40}} }
    });
}

// Hypertension distribution
const hyperLabels = {!! json_encode($hyperDist->keys()) !!};
const hyperData   = {!! json_encode($hyperDist->values()) !!};
if (hyperLabels.length > 0) {
    new Chart(document.getElementById('chartHyper'), {
        type: 'doughnut',
        data: {
            labels: hyperLabels,
            datasets: [{ data: hyperData, backgroundColor:['#1cc88a','#f6c23e','#fd7e14','#e74a3b'], borderWidth:2 }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
    });
}
</script>
@endsection