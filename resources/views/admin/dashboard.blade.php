@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap border-bottom pb-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Pusat Analitik Data Kesehatan (GARDA 5)</h2>
            <p class="text-muted mb-0">Fakultas Kesehatan Masyarakat Universitas Muhammadiyah Purwokerto</p>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="badge bg-dark p-2 text-uppercase fs-7 shadow-sm">
                Akses Sistem: <span class="text-warning fw-bold">{{ $currentRole }}</span>
            </span>
            <span class="badge bg-primary p-2 fs-7 shadow-sm ms-2">Sistem Stabil v1.5</span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-primary border-l-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Warga Binaan</h6>
                        <h2 class="fw-bold text-primary mb-0">{{ $kpi['total_warga'] }}</h2>
                    </div>
                    <div class="fs-1 text-light">👥</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-success border-l-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Log Masuk</h6>
                        <h2 class="fw-bold text-success mb-0">{{ $kpi['total_log'] }}</h2>
                    </div>
                    <div class="fs-1 text-light">📝</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-danger border-l-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Kasus High Sodium</h6>
                        <h2 class="fw-bold text-danger mb-0">{{ $kpi['high_sodium'] }}</h2>
                    </div>
                    <div class="fs-1 text-light">🚨</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-warning border-l-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-1">Rerata Sistolik</h6>
                        <h2 class="fw-bold text-warning mb-0">{{ $kpi['avg_sistolik'] }} <span class="fs-6 text-muted">mmHg</span></h2>
                    </div>
                    <div class="fs-1 text-light">❤️</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">📈 Tren Intensitas Keaktifan Warga</h5>
                    <span class="text-muted small">7 Hari Terakhir</span>
                </div>
                <div style="position: relative; height:320px;">
                    <canvas id="chartTrenKeaktifan"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-5">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3">⚥ Komposisi Gender Partisipan</h5>
                <div style="position: relative; height:250px; margin: auto;">
                    <canvas id="chartGender"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3">📊 Profil Status Hipertensi Komunitas</h5>
                <div style="position: relative; height:280px;">
                    <canvas id="chartHipertensi"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3">📍 Sebaran Warga per Wilayah Intervensi</h5>
                <div style="position: relative; height:280px; margin: auto;">
                    <canvas id="chartWilayahSpasial"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
        <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-white grow">💻 Live System Audit & Activity Stream</h5>
            <span class="spinner-grow spinner-grow-sm text-success" role="status"></span>
        </div>
        <div class="p-0 m-0 table-responsive" style="max-height: 280px; overflow-y: auto;">
            <table class="table table-hover table-striped mb-0 text-nowrap align-middle">
                <thead class="table-light sticky-top">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th>Aktor/User</th>
                        <th>Peran</th>
                        <th>Aksi Sistem</th>
                        <th>Rute Sasar</th>
                        <th class="px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                    <tr>
                        <td class="px-4 font-monospace text-muted small">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="fw-bold text-dark">{{ $log->user->name ?? 'Sistem Luar' }}</td>
                        <td><span class="badge bg-secondary">{{ $log->user->role ?? 'Guest' }}</span></td>
                        <td><span class="text-primary fw-bold">{{ $log->action }}</span> - <small class="text-muted">{{ $log->description }}</small></td>
                        <td><code class="text-danger bg-light px-2 py-1 rounded small">{{ $log->route }}</code></td>
                        <td class="px-4 text-muted small font-monospace">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada rekam jejak log audit masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // 1. Chart Tren Keaktifan Warga (Line)
    new Chart(document.getElementById('chartTrenKeaktifan'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($trenKesehatan, 'date')) !!},
            datasets: [{
                label: 'Jumlah Log Kesehatan Masuk',
                data: {!! json_encode(array_column($trenKesehatan, 'count')) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Chart Komposisi Gender Warga (Doughnut)
    new Chart(document.getElementById('chartGender'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($genderStats)) !!},
            datasets: [{
                data: {!! json_encode(array_values($genderStats)) !!},
                backgroundColor: ['#4e73df', '#f6c23e', '#36b9cc']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 3. Chart Profil Status Hipertensi (Bar)
    new Chart(document.getElementById('chartHipertensi'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($hipertensiStats)) !!},
            datasets: [{
                label: 'Jumlah Warga',
                data: {!! json_encode(array_values($hipertensiStats)) !!},
                backgroundColor: '#e74a3b',
                borderRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 4. Chart Sebaran Spasial Per Wilayah (Pie)
    new Chart(document.getElementById('chartWilayahSpasial'), {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_column($wilayahStats, 'nama_wilayah')) !!},
            datasets: [{
                data: {!! json_encode(array_column($wilayahStats, 'users_count')) !!},
                backgroundColor: ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endsection