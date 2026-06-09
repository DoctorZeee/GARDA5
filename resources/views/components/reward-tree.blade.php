@props(['leaves' => 0, 'points' => 0])

@php
    $totalLeaves = min((int) $leaves, 50);

    $level = match(true) {
        $totalLeaves >= 40 => ['num' => 5, 'name' => 'Pejuang Hidup',    'color' => '#1a7a4a'],
        $totalLeaves >= 25 => ['num' => 4, 'name' => 'Hutan Sehat',      'color' => '#2d9e5f'],
        $totalLeaves >= 12 => ['num' => 3, 'name' => 'Pohon Rindang',    'color' => '#40916c'],
        $totalLeaves >= 4  => ['num' => 2, 'name' => 'Tunas Berkembang', 'color' => '#52b788'],
        default            => ['num' => 1, 'name' => 'Bibit Harapan',    'color' => '#74c69d'],
    };

    // Slot daun: [cx, cy, rx, ry, rotate, warna-index]
    // Koordinat dalam viewBox 200x240, daun berbentuk ellipse menempel di cabang nyata
    $leafSlots = [
        // === MAHKOTA BAWAH (sekitar y=130) ===
        [72,  130, 14, 9,  -30, 0],
        [88,  124, 13, 8,  -10, 1],
        [100, 122, 14, 9,    0, 2],
        [112, 124, 13, 8,   10, 1],
        [128, 130, 14, 9,   30, 0],
        [64,  122, 12, 8,  -45, 3],
        [136, 122, 12, 8,   45, 3],

        // === MAHKOTA TENGAH (sekitar y=108) ===
        [76,  112, 14, 9,  -25, 2],
        [90,  105, 13, 8,   -8, 0],
        [100, 103, 15, 10,   0, 1],
        [110, 105, 13, 8,    8, 0],
        [124, 112, 14, 9,   25, 2],
        [68,  105, 12, 8,  -40, 3],
        [132, 105, 12, 8,   40, 3],

        // === MAHKOTA ATAS (sekitar y=88) ===
        [80,   94, 13, 8,  -20, 1],
        [91,   87, 14, 9,   -6, 2],
        [100,  84, 15, 10,   0, 0],
        [109,  87, 14, 9,    6, 2],
        [120,  94, 13, 8,   20, 1],
        [74,   90, 11, 7,  -35, 3],
        [126,  90, 11, 7,   35, 3],

        // === MAHKOTA PUNCAK (sekitar y=68) ===
        [85,   74, 12, 8,  -15, 0],
        [93,   67, 13, 8,   -4, 1],
        [100,  64, 14, 9,    0, 2],
        [107,  67, 13, 8,    4, 1],
        [115,  74, 12, 8,   15, 0],
        [79,   72, 10, 7,  -28, 3],
        [121,  72, 10, 7,   28, 3],

        // === PUNCAK TERTINGGI (sekitar y=50) ===
        [88,   56, 12, 8,  -10, 2],
        [96,   50, 13, 8,   -2, 0],
        [100,  47, 14, 9,    0, 1],
        [104,  50, 13, 8,    2, 0],
        [112,  56, 12, 8,   10, 2],
        [84,   55, 10, 7,  -22, 3],
        [116,  55, 10, 7,   22, 3],

        // === UJUNG RANTING KIRI ===
        [58,  138, 11, 7,  -50, 1],
        [50,  130, 10, 7,  -65, 2],
        [62,  120, 10, 7,  -42, 3],

        // === UJUNG RANTING KANAN ===
        [142, 138, 11, 7,   50, 1],
        [150, 130, 10, 7,   65, 2],
        [138, 120, 10, 7,   42, 3],

        // === FILL EXTRA ===
        [84,  116, 11, 7,  -12, 0],
        [116, 116, 11, 7,   12, 0],
        [84,   98, 11, 7,  -14, 1],
        [116,  98, 11, 7,   14, 1],
        [88,   78, 10, 7,  -10, 2],
        [112,  78, 10, 7,   10, 2],
        [92,   60, 10, 7,   -5, 3],
        [108,  60, 10, 7,    5, 3],
    ];

    $leafColors = ['#2d9e5f', '#40916c', '#52b788', '#1a7a4a'];
@endphp

<div class="tree-widget-wrap">
    <span class="tree-level-badge" style="background: {{ $level['color'] }};">
        Lv.{{ $level['num'] }} · {{ $level['name'] }}
    </span>

    <div class="tree-svg-stage">
        <svg viewBox="0 0 200 240" xmlns="http://www.w3.org/2000/svg" class="tree-svg">
            <defs>
                <linearGradient id="skyG" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#e8f5e9"/>
                    <stop offset="100%" stop-color="#f9fbe7"/>
                </linearGradient>
                <linearGradient id="trunkG" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#5d4037"/>
                    <stop offset="50%" stop-color="#8d6e63"/>
                    <stop offset="100%" stop-color="#6d4c41"/>
                </linearGradient>
                <linearGradient id="branchG" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#6d4c41"/>
                    <stop offset="100%" stop-color="#a1887f"/>
                </linearGradient>
                <radialGradient id="grassG" cx="50%" cy="30%">
                    <stop offset="0%" stop-color="#a5d6a7"/>
                    <stop offset="100%" stop-color="#66bb6a"/>
                </radialGradient>
            </defs>

            {{-- Background --}}
            <rect width="200" height="240" fill="url(#skyG)" rx="12"/>

            {{-- === CABANG DAN BATANG (digambar DULU, daun di atasnya) === --}}

            {{-- Batang utama --}}
            <rect x="93" y="158" width="14" height="68" rx="5" fill="url(#trunkG)"/>
            {{-- Tekstur batang --}}
            <line x1="97" y1="162" x2="96" y2="222" stroke="#4e342e" stroke-width="0.7" opacity="0.3"/>
            <line x1="103" y1="162" x2="104" y2="222" stroke="#4e342e" stroke-width="0.7" opacity="0.3"/>

            {{-- Cabang bawah kiri --}}
            <path d="M97 162 C90 158 78 150 65 143" stroke="#795548" stroke-width="5"
                  fill="none" stroke-linecap="round"/>
            {{-- Cabang bawah kanan --}}
            <path d="M103 162 C110 158 122 150 135 143" stroke="#795548" stroke-width="5"
                  fill="none" stroke-linecap="round"/>

            {{-- Cabang tengah kiri --}}
            <path d="M96 148 C86 140 74 132 62 124" stroke="#8d6e63" stroke-width="4"
                  fill="none" stroke-linecap="round"/>
            {{-- Cabang tengah kanan --}}
            <path d="M104 148 C114 140 126 132 138 124" stroke="#8d6e63" stroke-width="4"
                  fill="none" stroke-linecap="round"/>

            {{-- Cabang atas kiri --}}
            <path d="M96 130 C86 118 78 104 72 92" stroke="#a1887f" stroke-width="3"
                  fill="none" stroke-linecap="round"/>
            {{-- Cabang atas kanan --}}
            <path d="M104 130 C114 118 122 104 128 92" stroke="#a1887f" stroke-width="3"
                  fill="none" stroke-linecap="round"/>

            {{-- Batang tengah ke atas --}}
            <line x1="100" y1="158" x2="100" y2="50" stroke="#8d6e63" stroke-width="4"
                  stroke-linecap="round"/>

            {{-- Sub-cabang kiri atas --}}
            <path d="M94 110 C84 100 78 88 74 76" stroke="#bcaaa4" stroke-width="2"
                  fill="none" stroke-linecap="round"/>
            {{-- Sub-cabang kanan atas --}}
            <path d="M106 110 C116 100 122 88 126 76" stroke="#bcaaa4" stroke-width="2"
                  fill="none" stroke-linecap="round"/>

            {{-- === DAUN (digambar DI ATAS cabang) === --}}
            @if($totalLeaves === 0)
                {{-- Tunas kecil bila belum ada daun --}}
                <ellipse cx="100" cy="145" rx="8" ry="12" fill="#74c69d"
                         transform="rotate(-5 100 145)"/>
                <ellipse cx="100" cy="145" rx="8" ry="12" fill="#52b788"
                         transform="rotate(5 100 145)"/>
                <line x1="100" y1="157" x2="100" y2="145" stroke="#52b788" stroke-width="1.5"/>
            @else
                @foreach($leafSlots as $i => $s)
                    @if($i < $totalLeaves)
                    @php
                        [$cx, $cy, $rx, $ry, $rot, $ci] = $s;
                        $lc    = $leafColors[$ci];
                        $delay = round($i * 0.04, 2);
                        // Warna highlight lebih terang
                        $highlights = ['#52b788','#74c69d','#95d5b2','#40916c'];
                        $lh = $highlights[$ci];
                    @endphp
                    <g style="animation: leafSprout 0.45s {{ $delay }}s cubic-bezier(0.34,1.56,0.64,1) both;
                               transform-origin: {{ $cx }}px {{ $cy }}px;">
                        {{-- Body daun --}}
                        <ellipse cx="{{ $cx }}" cy="{{ $cy }}"
                                 rx="{{ $rx }}" ry="{{ $ry }}"
                                 fill="{{ $lc }}" opacity="0.93"
                                 transform="rotate({{ $rot }} {{ $cx }} {{ $cy }})"/>
                        {{-- Highlight daun --}}
                        <ellipse cx="{{ $cx - 2 }}" cy="{{ $cy - 1 }}"
                                 rx="{{ round($rx * 0.45) }}" ry="{{ round($ry * 0.4) }}"
                                 fill="{{ $lh }}" opacity="0.45"
                                 transform="rotate({{ $rot }} {{ $cx }} {{ $cy }})"/>
                        {{-- Urat daun --}}
                        <line x1="{{ $cx }}" y1="{{ $cy - $ry }}"
                              x2="{{ $cx }}" y2="{{ $cy + $ry }}"
                              stroke="#1b5e20" stroke-width="0.4" opacity="0.35"
                              transform="rotate({{ $rot }} {{ $cx }} {{ $cy }})"/>
                    </g>
                    @endif
                @endforeach
            @endif

            {{-- Tanah --}}
            <ellipse cx="100" cy="226" rx="55" ry="8" fill="url(#grassG)" opacity="0.7"/>
            <rect x="50" y="223" width="100" height="14" rx="7" fill="#66bb6a"/>
            <rect x="58" y="220" width="84" height="8"  rx="4" fill="#81c784"/>
        </svg>
    </div>

    <div class="tree-stats-row">
        <div class="tree-stat-item">
            <span class="tree-stat-num" style="color: var(--warning-gold);">{{ $points }}</span>
            <span class="tree-stat-lbl">poin</span>
        </div>
        <div class="tree-stat-divider"></div>
        <div class="tree-stat-item">
            <span class="tree-stat-num" style="color: {{ $level['color'] }};">{{ $leaves }}</span>
            <span class="tree-stat-lbl">daun tumbuh</span>
        </div>
    </div>

    @if($totalLeaves < 50)
    <div class="tree-progress-outer">
        <div class="tree-progress-inner"
             style="width: {{ round(($totalLeaves / 50) * 100) }}%; background: {{ $level['color'] }};"></div>
    </div>
    <p class="tree-progress-label">{{ 50 - $totalLeaves }} daun lagi untuk pohon penuh 🌿</p>
    @else
    <p class="tree-progress-label" style="color: var(--leaf-green-dark); font-weight: 700;">
        🏆 Pohon Anda sudah rindang sempurna!
    </p>
    @endif
</div>
