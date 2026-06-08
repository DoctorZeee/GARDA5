@props(['points' => 0])

@php
    // Logika level berdasarkan rentang poin (Gamifikasi)
    $level = 1; // Bibit
    if ($points >= 100) $level = 2; // Tunas
    if ($points >= 300) $level = 3; // Pohon Muda
    if ($points >= 600) $level = 4; // Pohon Besar
    if ($points >= 1000) $level = 5; // Pohon Sehat

    $titles = [
        1 => 'Level 1: Bibit Harapan',
        2 => 'Level 2: Tunas Berkembang',
        3 => 'Level 3: Pohon Muda',
        4 => 'Level 4: Pohon Rindang',
        5 => 'Level 5: Pohon Sehat Purna'
    ];
@endphp

<div class="text-center p-6 bg-linear-to-b from-green-50 to-green-100 rounded-3xl shadow-lg border-2 border-green-200">
    <h3 class="text-2xl font-bold text-green-900 mb-4">{{ $titles[$level] }}</h3>
    
    <div class="flex justify-center items-center h-48 w-48 mx-auto bg-white rounded-full shadow-inner mb-6 border-4 border-green-300">
        @if($level == 1)
            <span class="text-7xl" title="Bibit">🌱</span>
        @elseif($level == 2)
            <span class="text-7xl" title="Tunas">🌿</span>
        @elseif($level == 3)
            <span class="text-7xl" title="Pohon Muda">🪴</span>
        @elseif($level == 4)
            <span class="text-7xl" title="Pohon Besar">🌳</span>
        @else
            <span class="text-7xl" title="Pohon Sehat">🌲✨</span>
        @endif
    </div>

    <p class="text-gray-700 font-medium text-lg">Total Poin Kesehatan: <span class="text-green-700 font-extrabold text-2xl">{{ $points }}</span></p>
    
    @if($level < 5)
        <div class="w-full bg-gray-300 rounded-full h-4 mt-4 overflow-hidden shadow-inner">
            <div class="bg-green-500 h-4 rounded-full transition-all duration-500" style="width: {{ min(($points % 300) / 3, 100) }}%"></div>
        </div>
        <p class="text-sm text-gray-600 mt-3 font-medium">Terus catat tensi harian untuk merawat pohonmu!</p>
    @else
        <p class="text-sm text-green-700 mt-3 font-bold">Luar biasa! Anda telah mencapai level tertinggi.</p>
    @endif
</div>