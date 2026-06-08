@extends('layouts.kader') @section('content')
<fieldset class="filter-controls-groupbox mb-4 shadow-sm">
    <legend><i class="fa-solid fa-filter"></i> Sistem Penyaringan Data</legend>
    <div class="filter-controls-grid">
        <div class="filter-box-item">
            <label for="filterStatus">Status Hipertensi</label>
            <select id="filterStatus" class="filter-select-engine">
                <option value="all">Semua Kategori</option>
                <option value="Normal">Normal</option>
                <option value="Ringan">Hipertensi Ringan</option>
                <option value="Sedang">Hipertensi Sedang</option>
                <option value="Berat">Hipertensi Berat</option>
            </select>
        </div>
    </div>
</fieldset>

<div class="table-card-wrapper shadow-sm bg-white p-3 rounded">
    <table id="dynamicKaderDataTable" class="display cell-border nowrap" style="width:100%">
        <thead>
            <tr>
                <th>Tanggal Input</th>
                <th>Nama Lengkap</th>
                <th>Tekanan Darah</th>
                <th>BB (kg)</th>
                <th>Garam</th>
                <th>Status Hipertensi</th>
                <th>Poin User</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wargaLogs as $log)
            <tr>
                <td>{{ $log->tanggal_input->format('Y-m-d') }}</td>
                <td>{{ $log->user->nama_lengkap }}</td>
                <td>{{ $log->tekanan_darah ?? '-' }}</td>
                <td>{{ $log->berat_badan }}</td>
                <td>
                    @if($log->konsumsi_garam === 'less') < 5 gram
                    @elseif($log->konsumsi_garam === 'ideal') 5 gram
                    @else > 5 gram @endif
                </td>
                <td>
                    @php
                        $badgeClass = match($log->status_hipertensi) {
                            'Normal' => 'bg-success',
                            'Ringan' => 'bg-warning text-dark',
                            'Sedang' => 'bg-orange', /* Custom CSS dibutuhkan */
                            'Berat' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $log->status_hipertensi }}</span>
                </td>
                <td>{{ $log->user->point->total_points ?? 0 }} Poin</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#dynamicKaderDataTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: { searchPlaceholder: "Cari nama/data..." }
        });

        // Hubungkan Dropdown Filter Status dengan Kolom DataTables (Kolom ke-5 / Index 5)
        $('#filterStatus').on('change', function() {
            var val = $(this).val();
            if(val !== 'all') {
                table.column(5).search(val).draw();
            } else {
                table.column(5).search('').draw();
            }
        });
    });
</script>
@endsection