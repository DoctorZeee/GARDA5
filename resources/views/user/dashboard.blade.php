@extends('layouts.user')

@section('content')
    <header class="app-header-reward shadow-sm position-relative">
        <div class="badge-success-garam position-absolute top-0 start-0 m-2 z-3">
            <span>Target: Maksimal 5 Gram Garam/Hari</span>
        </div>

        <div class="point-badge-center ms-auto me-4 mt-3 z-3">
            <div class="point-circle">
                <span class="point-label">Poin</span>
                <span class="point-value" id="currentPointVal">{{ $user->point->total_points ?? 0 }}</span>
            </div>
        </div>

        <div class="reward-tree-box position-absolute bottom-0 end-0 me-3 mb-2" id="rewardTreeWorkspace">
            <svg class="tree-skeleton-svg" viewBox="0 0 120 120">
                <path
                    d="M52,120 C54,95 48,75 54,55 C56,42 42,32 35,25 C45,30 52,40 56,48 C58,35 68,20 82,12 C72,24 64,38 62,50 C64,72 63,95 66,120 Z"
                    fill="#6f4e37" />
                <path d="M54,75 C42,65 30,62 18,58 C28,60 38,65 48,72 Z" fill="#5c4033" />
                <path d="M60,68 C72,58 85,52 98,48 C86,52 74,60 62,66 Z" fill="#5c4033" />
            </svg>
            <div class="leaves-cluster-layer" id="leavesContainer"></div>
        </div>
    </header>

    <div class="px-3 pt-3">
        @if (session('success'))
            <div class="alert alert-success py-2 mb-0" style="font-size: 13px;">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 mb-0" style="font-size: 13px;">{{ session('error') }}</div>
        @endif
    </div>

    <section class="user-profile-summary-card">
        <div class="user-avatar-placeholder"><i class="fa-solid fa-user-astronaut"></i></div>
        <div class="user-meta-details grow">
            <h3>{{ $user->nama_lengkap }}</h3>
            <p><i class="fa-solid fa-map-location-dot"></i> Wilayah: {{ $user->wilayah->nama_wilayah ?? 'Belum Diatur' }}
            </p>
            <div class="badge-row">
                <span class="meta-pill"><i class="fa-solid fa-star"></i> <span
                        id="pillPoints">{{ $user->point->total_points ?? 0 }}</span> Poin</span>
                <span class="meta-pill"><i class="fa-solid fa-leaf text-success"></i> <span
                        id="pillLeaves">{{ $user->point->total_leaves ?? 0 }}</span> Daun</span>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-link text-danger p-0" title="Keluar"><i
                    class="fa-solid fa-power-off fa-lg"></i></button>
        </form>
    </section>

    <section class="app-card-section">
        <div class="card-section-title">
            <i class="fa-solid fa-file-medical text-primary"></i>
            <h4 class="mb-0">Pencatatan Kesehatan Harian</h4>
        </div>

        @if ($hasLoggedToday)
            <div class="alert alert-info py-2" style="font-size: 12px;">
                <i class="fa-solid fa-circle-check"></i> Anda sudah mencatat data kesehatan hari ini. Hebat! Kembali besok
                untuk daun tambahan.
            </div>
        @else
            <form action="{{ route('user.health-logs.store') }}" method="POST" class="grid-form-inputs">
                @csrf
                <div class="input-field-block">
                    <label>Tekanan Darah (mmHg)</label>
                    <input type="text" name="tekanan_darah" placeholder="Contoh: 120/80" pattern="\d{2,3}/\d{2,3}">
                </div>
                <div class="input-field-block-double">
                    <div class="input-field-block">
                        <label>Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="berat_badan"
                            value="{{ old('berat_badan', $user->berat_badan) }}" required>
                    </div>
                    <div class="input-field-block">
                        <label>Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan" required>
                    </div>
                </div>
                <div class="input-field-block">
                    <label>Konsumsi Garam Hari Ini</label>
                    <select name="konsumsi_garam" required>
                        <option value="" disabled selected>-- Pilih Tingkat Konsumsi --</option>
                        <option value="less">&lt; 5 Gram (Sangat Sehat)</option>
                        <option value="ideal">5 Gram (Sangat Ideal / 1 sdt)</option>
                        <option value="more">&gt; 5 Gram (Berlebih)</option>
                    </select>
                </div>
                <div class="input-field-block">
                    <label>Keluhan / Gejala</label>
                    <textarea name="keluhan" rows="2" placeholder="Pusing, tengkuk kaku, dll"></textarea>
                </div>
                <button type="submit" class="btn-action-submit success-gradient w-100">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Simpan & Dapatkan +1 Daun
                </button>
            </form>
        @endif
    </section>

    <section class="app-card-section">
        <div class="card-section-title">
            <i class="fa-solid fa-clapperboard text-danger"></i>
            <h4 class="mb-0">Pojok Video Edukasi Sehat</h4>
        </div>
        <div class="video-horizontal-scroll-deck d-flex gap-3 overflow-x-auto pb-2">
            @foreach ($videos as $vid)
                <div class="video-playlist-card shrink-0" style="width: 140px; cursor: pointer;"
                    onclick="playEducationalVideo('{{ $vid->youtube_id }}', '{{ $vid->title }}', {{ $vid->id }})">
                    <div class="video-thumbnail-wrapper position-relative rounded overflow-hidden"
                        style="height: 80px; background: #000;">
                        <img src="https://img.youtube.com/vi/{{ $vid->youtube_id }}/hqdefault.jpg"
                            class="w-100 h-100 object-fit-cover opacity-75" alt="Thumbnail">
                        <div class="play-overlay-icon position-absolute top-50 start-50 translate-middle text-white fs-3"><i
                                class="fa-solid fa-circle-play"></i></div>
                    </div>
                    <h5 class="mt-2 text-truncate" style="font-size: 11px;">{{ $vid->title }}</h5>
                </div>
            @endforeach
        </div>
    </section>

    <div class="daily-checkin-footer-bar text-center bg-white p-2 border-top position-fixed bottom-0 w-100 z-3"
        style="max-width: 450px;">
        @if ($hasCheckedInToday)
            <button class="btn btn-secondary w-100" disabled><i class="fa-solid fa-circle-check"></i> Check-In Hari Ini
                Selesai</button>
        @else
            <form action="{{ route('user.checkin') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-warning w-100 fw-bold"><i class="fa-solid fa-calendar-check"></i>
                    Ambil Poin Check-In Harian (+1 Poin)</button>
            </form>
        @endif
    </div>

    <div class="video-modal-backdrop position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none justify-content-center align-items-center z-3"
        id="videoModalPlayer" style="padding: 15px;">
        <div class="video-modal-content bg-white rounded w-100" style="max-width: 400px; overflow:hidden;">
            <div class="video-modal-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold" id="videoModalTitle">Judul Video</h6>
                <button type="button" class="btn-close" onclick="closeEducationalVideoModal()"></button>
            </div>
            <div class="iframe-responsive-container" style="position: relative; padding-bottom: 56.25%;">
                <div id="youtubePlayerPlaceholder" class="position-absolute top-0 start-0 w-100 h-100"></div>
            </div>
            <div class="video-modal-footer p-3 text-center">
                <button type="button" class="btn btn-secondary w-100 fw-bold" id="btnClaimVideoReward" disabled>Tonton 5
                    Detik Untuk Klaim</button>
            </div>
        </div>
    </div>

    <script>
    const userLeaves = {{ $user->point->total_leaves ?? 0 }};

    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById('leavesContainer');
        if (!container) return;

        // Zona dasar untuk daun sedikit
        const baseZones = [
            { minX: 25, maxX: 45, minY: 12, maxY: 32 },
            { minX: 50, maxX: 72, minY: 10, maxY: 30 },
            { minX: 20, maxX: 38, minY: 30, maxY: 52 },
            { minX: 56, maxX: 76, minY: 32, maxY: 58 },
            { minX: 38, maxX: 56, minY: 22, maxY: 48 }
        ];

        // Zona tambahan untuk daun banyak (lebih luas, mencakup sedikit batang atas)
        const extendedZones = [
            { minX: 10, maxX: 28, minY: 18, maxY: 42 },
            { minX: 70, maxX: 90, minY: 18, maxY: 42 },
            { minX: 28, maxX: 50, minY: 40, maxY: 62 },
            { minX: 50, maxX: 72, minY: 40, maxY: 62 }
        ];

        // Gabungkan zona sesuai total daun
        let zones = [...baseZones];
        if (userLeaves > 40) zones = zones.concat(extendedZones);
        if (userLeaves > 80) {
            // Perlebar lagi jika sangat banyak
            zones.push(
                { minX: 5, maxX: 20, minY: 15, maxY: 45 },
                { minX: 80, maxX: 95, minY: 15, maxY: 45 }
            );
        }

        // Fungsi ukuran daun dinamis
        function getLeafSizeRange(total) {
            if (total <= 30) return { min: 6, max: 10 };
            if (total <= 70) return { min: 5, max: 8 };
            if (total <= 120) return { min: 4, max: 7 };
            if (total <= 200) return { min: 3, max: 5 };
            return { min: 2, max: 4 };
        }

        const sizeRange = getLeafSizeRange(userLeaves);
        const maxRender = Math.min(userLeaves, 250); // batasi agar browser tidak berat

        for (let i = 0; i < maxRender; i++) {
            const leaf = document.createElement('div');
            leaf.className = 'growing-leaf-node';

            // Ukuran acak dalam rentang
            const size = sizeRange.min + Math.floor(Math.random() * (sizeRange.max - sizeRange.min + 1));
            leaf.style.width = size + 'px';
            leaf.style.height = size + 'px';

            // Variasi bentuk
            const shapeRand = Math.random();
            if (shapeRand < 0.3) {
                leaf.style.borderRadius = '50% 0 50% 0';
            } else if (shapeRand < 0.6) {
                leaf.style.borderRadius = '0 50% 0 50%';
            } else if (shapeRand < 0.85) {
                leaf.style.borderRadius = '50%';
            } else {
                leaf.style.borderRadius = '40% 60% 30% 70%';
            }

            // Warna hijau natural
            const green = Math.floor(Math.random() * 55) + 70;
            const red = Math.floor(Math.random() * 25) + 15;
            leaf.style.backgroundColor = `rgb(${red}, ${green}, 20)`;
            leaf.style.opacity = (0.7 + Math.random() * 0.3).toFixed(2);

            // Pilih zona acak
            const zone = zones[Math.floor(Math.random() * zones.length)];
            const left = zone.minX + Math.random() * (zone.maxX - zone.minX);
            const top = zone.minY + Math.random() * (zone.maxY - zone.minY);
            leaf.style.left = left + '%';
            leaf.style.top = top + '%';

            leaf.style.transform = `rotate(${Math.floor(Math.random() * 360)}deg)`;
            leaf.style.zIndex = Math.floor(Math.random() * 5) + 1;

            container.appendChild(leaf);
        }

        // Jika ada sisa daun yang tidak dirender, tampilkan badge kecil
        if (userLeaves > maxRender) {
            const remainder = document.createElement('div');
            remainder.style.position = 'absolute';
            remainder.style.bottom = '5px';
            remainder.style.right = '5px';
            remainder.style.fontSize = '9px';
            remainder.style.color = '#2d6a4f';
            remainder.style.fontWeight = 'bold';
            remainder.style.zIndex = '10';
            remainder.innerText = `+${userLeaves - maxRender}`;
            container.appendChild(remainder);
        }
    });
</script>
@endsection
