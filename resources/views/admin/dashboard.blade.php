@extends('layouts.admin')

@section('content')
<section class="row g-3 mb-4">
    @foreach([['Warga', $totalWarga, 'text-primary'], ['Logs', $totalLog, 'text-success'], ['High Sodium', $avgGaram, 'text-danger']] as $kpi)
    <div class="col-md-4">
        <div class="card p-3 border-0 shadow-sm text-center">
            <h6 class="text-muted">{{ $kpi[0] }}</h6>
            <h3 class="fw-bold {{ $kpi[2] }}">{{ $kpi[1] }}</h3>
        </div>
    </div>
    @endforeach
</section>

<section class="row mb-4">
    <div class="col-md-6">
        <div class="card p-3 border-0 shadow-sm">
            <h6>Tren Input Data (7 Hari Terakhir)</h6>
            <canvas id="lineChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 border-0 shadow-sm">
            <h6>Warga per Wilayah</h6>
            <canvas id="wilayahChart"></canvas>
        </div>
    </div>
</section>

<script>
    // Contoh implementasi Chart JS untuk Tren
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($trenKesehatan->pluck('date')) !!},
            datasets: [{ label: 'Jumlah Input', data: {!! json_encode($trenKesehatan->pluck('count')) !!}, borderColor: '#2a9d8f' }]
        }
    });
    
    // Contoh implementasi Chart JS untuk Wilayah
    new Chart(document.getElementById('wilayahChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($wilayahStats->pluck('nama_wilayah')) !!},
            datasets: [{ data: {!! json_encode($wilayahStats->pluck('users_count')) !!}, backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'] }]
        }
    });
</script>
@endsection