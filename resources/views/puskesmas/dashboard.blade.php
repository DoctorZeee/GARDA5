@extends('layouts.puskesmas') @section('content')
<section class="metrics-summary-row-grid mb-4">
    <div class="metric-analytic-card">
        <div class="card-icon-box bg-light-blue"><i class="fa-solid fa-users text-primary"></i></div>
        <div class="card-numeric-info">
            <h3>{{ number_format($totalWarga) }}</h3>
            <p>Jumlah Warga Aktif</p>
        </div>
    </div>
    <div class="metric-analytic-card">
        <div class="card-icon-box bg-light-success"><i class="fa-solid fa-file-medical text-success"></i></div>
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
</section>

<section class="charts-visualizations-matrix-grid">
    <div class="chart-canvas-card shadow-sm">
        <h5><i class="fa-solid fa-wave-square text-danger"></i> Tren Rata-Rata Tekanan Darah (7 Hari Terakhir)</h5>
        <div class="canvas-holder">
            <canvas id="chartTekananDarah"></canvas>
        </div>
    </div>
    </section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctxTD = document.getElementById('chartTekananDarah').getContext('2d');
        const chartData = @json($chartTD);

        new Chart(ctxTD, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    { label: 'Sistolik (Avg)', data: chartData.sistolik, borderColor: '#d90429', tension: 0.3, fill: false },
                    { label: 'Diastolik (Avg)', data: chartData.diastolik, borderColor: '#4361ee', tension: 0.3, fill: false }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    });
</script>
@endsection