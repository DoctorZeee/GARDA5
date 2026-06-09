@extends('layouts.puskesmas')

@section('content')

{{-- KPI Cards --}}
<div class="metrics-summary-row-grid mb-4">
    <div class="metric-analytic-card">
        <div class="card-icon-box bg-light-blue"><i class="fa-solid fa-users puskesmas-accent"></i></div>
        <div class="card-numeric-info">
            <h3>{{ number_format($totalWarga) }}</h3>
            <p>Warga Aktif</p>
        </div>
    </div>
    <div class="metric-analytic-card">
        <div class="card-icon-box bg-light-success"><i class="fa-solid fa-file-waveform text-success"></i></div>
        <div class="card-numeric-info">
            <h3>{{ number_format($totalLogs) }}</h3>
            <p>Total Log Kesehatan</p>
        </div>
    </div>
    <div class="metric-analytic-card">
        <div class="card-icon-box bg-light-warning"><i class="fa-solid fa-star text-warning"></i></div>
        <div class="card-numeric-info">
            <h3>{{ number_format($totalPoin) }}</h3>
            <p>Total Poin Warga</p>
        </div>
    </div>
    <div class="metric-analytic-card">
        <div class="card-icon-box bg-light-success"><i class="fa-solid fa-leaf text-success"></i></div>
        <div class="card-numeric-info">
            <h3>{{ number_format($totalDaun) }}</h3>
            <p>Total Daun Tumbuh</p>
        </div>
    </div>
</div>

{{-- Chart + Panel Interpretasi --}}
<div class="charts-visualizations-matrix-grid">
    <div class="chart-canvas-card shadow-sm">
        <h5><i class="fa-solid fa-heart-pulse text-danger me-1"></i> Tren Rata-Rata Tekanan Darah (7 Hari Terakhir)</h5>
        <div class="canvas-holder" style="height: 300px;">
            <canvas id="chartTekananDarah"></canvas>
        </div>
    </div>

    <div class="chart-canvas-card shadow-sm">
        <h5><i class="fa-solid fa-circle-info text-primary me-1"></i> Panduan Interpretasi Tekanan Darah</h5>

        <div class="interpretasi-item interpretasi-normal">
            <span class="interpretasi-badge">Normal</span>
            &lt; 120 / &lt; 80 mmHg
        </div>
        <div class="interpretasi-item interpretasi-ringan">
            <span class="interpretasi-badge">Ringan</span>
            130–139 / 85–89 mmHg
        </div>
        <div class="interpretasi-item interpretasi-sedang">
            <span class="interpretasi-badge">Sedang</span>
            140–159 / 90–99 mmHg
        </div>
        <div class="interpretasi-item interpretasi-berat">
            <span class="interpretasi-badge">Berat</span>
            ≥ 160 / ≥ 100 mmHg
        </div>

        <hr>
        <div class="stat-center-box">
            <div class="stat-number">{{ number_format($totalLogs) }}</div>
            <div class="stat-desc">rekam medis tersimpan</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="{{ csp_nonce() }}"></script>
<script nonce="{{ csp_nonce() }}">
document.addEventListener('DOMContentLoaded', function () {
    const data = @json($chartTD);
    new Chart(document.getElementById('chartTekananDarah'), {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Sistolik (avg)',
                    data: data.sistolik,
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231,74,59,0.08)',
                    tension: 0.4, fill: true, pointRadius: 5,
                    pointBackgroundColor: '#e74a3b'
                },
                {
                    label: 'Diastolik (avg)',
                    data: data.diastolik,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78,115,223,0.08)',
                    tension: 0.4, fill: true, pointRadius: 5,
                    pointBackgroundColor: '#4e73df'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: {
                    beginAtZero: false,
                    suggestedMin: 60,
                    suggestedMax: 180,
                    title: { display: true, text: 'mmHg' }
                }
            }
        }
    });
});
</script>
@endsection
