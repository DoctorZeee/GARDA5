@extends('layouts.user')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-2">Halo, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-xl text-gray-600">Bagaimana kesehatan Anda hari ini? Mari luangkan waktu 1 menit untuk mencatatnya.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <div class="flex flex-col gap-6">
            <x-reward-tree :points="Auth::user()->point->total_points ?? 0" />

            <form action="{{ route('user.checkin') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-yellow-400 text-yellow-900 text-xl font-bold py-5 px-6 rounded-3xl shadow-md hover:bg-yellow-500 transition-colors flex justify-center items-center gap-3">
                    <span class="text-3xl">📅</span> Klaim Poin Kehadiran Hari Ini
                </button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 border-t-8 border-blue-500">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                <span>📝</span> Catat Data Kesehatan
            </h2>

            @if(session('success'))
                <div class="bg-green-100 border-l-8 border-green-500 text-green-800 p-5 mb-8 rounded-xl text-lg font-medium shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('user.health-logs.store') }}" method="POST">
                @csrf
                
                <div class="mb-8">
                    <label class="block text-2xl font-semibold text-gray-700 mb-4">Tekanan Darah (mmHg)</label>
                    <div class="flex items-center gap-4">
                        <input type="number" name="sistolik" placeholder="120" required 
                            class="w-full text-3xl p-5 border-4 border-gray-200 rounded-2xl focus:ring-0 focus:border-blue-500 text-center transition-colors">
                        <span class="text-5xl text-gray-300 font-light">/</span>
                        <input type="number" name="diastolik" placeholder="80" required 
                            class="w-full text-3xl p-5 border-4 border-gray-200 rounded-2xl focus:ring-0 focus:border-blue-500 text-center transition-colors">
                    </div>
                    <p class="text-gray-500 mt-2 text-base">Atas (Sistolik) / Bawah (Diastolik)</p>
                </div>

                <div class="mb-10">
                    <label class="block text-2xl font-semibold text-gray-700 mb-4">Estimasi Dosis Garam</label>
                    <select name="kadar_garam" required 
                        class="w-full text-xl p-5 border-4 border-gray-200 rounded-2xl focus:ring-0 focus:border-blue-500 bg-gray-50 cursor-pointer appearance-none transition-colors">
                        <option value="" disabled selected>👉 Ketuk untuk memilih...</option>
                        <option value="rendah">✅ Rendah (Kurang dari 1/2 sendok teh)</option>
                        <option value="sedang">⚠️ Sedang (Sekitar 1 sendok teh)</option>
                        <option value="tinggi">🚨 Tinggi (Makanan sangat asin / Kuah)</option>
                    </select>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 text-white text-2xl font-extrabold py-6 rounded-3xl shadow-lg hover:bg-blue-700 hover:-translate-y-1 transition-all active:scale-95">
                    Simpan Data Sekarang
                </button>
            </form>
        </div>

    </div>
</div>
@endsection