@extends('layouts.user')

@section('content')

@php
    $points = Auth::user()->point->total_points ?? 0;
    $leaves = Auth::user()->point->total_leaves ?? 0;
@endphp

{{-- Greeting --}}
<div class="greeting-block">
    <h1>Halo, {{ Auth::user()->nama_lengkap }}! 👋</h1>
    <p>Bagaimana kesehatan Anda hari ini? Catat yuk — setiap aktivitas menumbuhkan daun di pohonmu!</p>
</div>

{{-- Pohon Reward SVG --}}
<x-reward-tree :points="$points" :leaves="$leaves" />

{{-- Check-in Harian --}}
@if(!$hasCheckedInToday)
    <form action="{{ route('user.checkin') }}" method="POST">
        @csrf
        <button type="submit" class="checkin-btn">
            <i class="fa-solid fa-calendar-check"></i>
            Klaim Poin Kehadiran Hari Ini (+1 Poin)
        </button>
    </form>
@else
    <div class="checkin-done">✅ Check-in hari ini sudah diklaim</div>
@endif

{{-- Form Catat Kesehatan --}}
<div class="user-section-card">
    <div class="card-head" style="border-left: 4px solid var(--formal-blue-accent);">
        <i class="fa-solid fa-notes-medical" style="color: var(--formal-blue-accent); font-size: 18px;"></i>
        <h2>Catat Data Kesehatan</h2>
        <span class="card-head-meta">+1 poin per entri</span>
    </div>
    <div class="user-card-body">
        @if($hasLoggedToday)
            <div class="user-alert user-alert-info">
                ✅ Anda sudah mencatat data kesehatan hari ini. Sampai jumpa besok!
            </div>
        @else
            @if($errors->any())
                <div class="user-alert user-alert-error">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.health-logs.store') }}" method="POST">
                @csrf

                {{-- Tekanan Darah --}}
                <div class="form-group-user">
                    <label class="user-form-label">
                        <i class="fa-solid fa-heart-pulse" style="color: var(--danger-red);"></i>
                        Tekanan Darah (mmHg)
                        <span style="color: #94a3b8; font-weight: 400;"> — opsional</span>
                    </label>
                    <div class="td-pair">
                        <input type="number" id="sistolik" placeholder="120" min="60" max="250"
                            class="user-form-input user-form-input-center"
                            oninput="gabungTD()">
                        <div class="td-separator">/</div>
                        <input type="number" id="diastolik" placeholder="80" min="40" max="150"
                            class="user-form-input user-form-input-center"
                            oninput="gabungTD()">
                    </div>
                    <input type="hidden" name="tekanan_darah" id="tekanan_darah">
                    <p class="form-helper-text">Sistolik (atas) / Diastolik (bawah). Biarkan kosong jika tidak diukur.</p>
                </div>

                {{-- Berat & Tinggi Badan --}}
                <div class="form-row-double form-group-user">
                    <div>
                        <label class="user-form-label">
                            <i class="fa-solid fa-weight-scale" style="color: var(--formal-blue-accent);"></i> Berat Badan (kg)
                        </label>
                        <input type="number" name="berat_badan" step="0.1" min="10" max="300"
                            value="{{ old('berat_badan') }}" placeholder="65" required
                            class="user-form-input user-form-input-center">
                    </div>
                    <div>
                        <label class="user-form-label">
                            <i class="fa-solid fa-ruler-vertical" style="color: var(--formal-blue-accent);"></i> Tinggi Badan (cm)
                        </label>
                        <input type="number" name="tinggi_badan" min="50" max="250"
                            value="{{ old('tinggi_badan') }}" placeholder="160" required
                            class="user-form-input user-form-input-center">
                    </div>
                </div>

                {{-- Konsumsi Garam --}}
                <div class="form-group-user">
                    <label class="user-form-label">
                        <i class="fa-solid fa-shaker" style="color: var(--warning-gold);"></i> Estimasi Konsumsi Garam Harian
                    </label>
                    <select name="konsumsi_garam" required class="user-form-input">
                        <option value="" disabled selected>— Pilih salah satu —</option>
                        <option value="less"  {{ old('konsumsi_garam') === 'less'  ? 'selected' : '' }}>✅ Rendah — Kurang dari 5 gram/hari</option>
                        <option value="ideal" {{ old('konsumsi_garam') === 'ideal' ? 'selected' : '' }}>⚠️ Ideal — Sekitar 5 gram/hari</option>
                        <option value="more"  {{ old('konsumsi_garam') === 'more'  ? 'selected' : '' }}>🚨 Tinggi — Lebih dari 5 gram/hari</option>
                    </select>
                </div>

                {{-- Keluhan --}}
                <div class="form-group-user">
                    <label class="user-form-label">
                        <i class="fa-solid fa-comment-medical" style="color: #94a3b8;"></i>
                        Keluhan <span style="color: #94a3b8; font-weight: 400;"> — opsional</span>
                    </label>
                    <textarea name="keluhan" rows="2" maxlength="500"
                        class="user-form-input"
                        placeholder="Contoh: pusing, mudah lelah, bengkak...">{{ old('keluhan') }}</textarea>
                </div>

                <button type="submit" class="btn-submit-health">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data Kesehatan
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Video Edukasi --}}
@if($videos->count())
<div class="user-section-card">
    <div class="card-head" style="border-left: 4px solid var(--leaf-green-dark);">
        <i class="fa-solid fa-graduation-cap" style="color: var(--leaf-green-dark); font-size: 18px;"></i>
        <h2>Video Edukasi</h2>
        <span class="card-head-meta">Tonton & klaim poin</span>
    </div>
    <div class="user-card-body">
        @foreach($videos as $video)
        <div class="video-item">
            <div class="video-title">{{ $video->title }}</div>
            <div class="video-iframe-wrap">
                <iframe src="https://www.youtube.com/embed/{{ $video->youtube_id }}"
                    allowfullscreen loading="lazy"></iframe>
            </div>
            <form action="{{ route('user.video.claim', $video->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-claim-video">
                    🌿 +{{ $video->points_reward }} Poin — Sudah Ditonton
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

<script>
function gabungTD() {
    var s = document.getElementById('sistolik').value;
    var d = document.getElementById('diastolik').value;
    document.getElementById('tekanan_darah').value = (s && d) ? s + '/' + d : '';
}
</script>
@endsection
