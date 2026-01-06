@extends('layouts.app')

@section('title', 'Dashboard - Kollej Davomat Tizimi')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    <!-- Statistika Kartochkalari -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Jami Guruhlar -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Jami Guruhlar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $jamiGuruhlar }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Jami Talabalar -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Aktiv Talabalar</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $jamiTalabalar }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Bugungi Davomat -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bugun Davomat Olingan</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $bugungiStatistika['davomat_olingan'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Bugungi Davomat Foizi -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Bugungi Davomat</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $bugungiStatistika['foiz'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Bugungi va Haftalik Statistika -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Bugungi Statistika -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Bugungi Davomat</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $bugungiStatistika['bor'] }}</p>
                    <p class="text-sm text-green-700">Bor</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $bugungiStatistika['yoq'] }}</p>
                    <p class="text-sm text-red-700">Yo'q</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Davomat foizi</span>
                    <span>{{ $bugungiStatistika['foiz'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500"
                         style="width: {{ $bugungiStatistika['foiz'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Haftalik O'rtacha -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 Haftalik O'rtacha</h3>
            <div class="flex items-center justify-center h-32">
                <div class="text-center">
                    <p class="text-5xl font-bold text-blue-600">{{ $haftalikOrtacha }}%</p>
                    <p class="text-gray-500 mt-2">Bu hafta davomat</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-500"
                         style="width: {{ $haftalikOrtacha }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kunlik Trend va Guruhlar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kunlik Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📉 Oxirgi 7 Kunlik Trend</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Eng Ko'p Yo'q Bo'lgan Talabalar -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">⚠️ Eng Ko'p Yo'qliklar</h3>
            @if(count($engKopYoqTalabalar) > 0)
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($engKopYoqTalabalar as $index => $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center min-w-0">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold
                            {{ $index < 3 ? 'bg-red-100 text-red-600' : 'bg-gray-200 text-gray-600' }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="ml-3 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item['talaba']->fish }}</p>
                            <p class="text-xs text-gray-500">{{ $item['talaba']->guruh?->nomi ?? 'Guruhsiz' }}</p>
                        </div>
                    </div>
                    <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-600">
                        {{ $item['yoq_soni'] }} yo'q
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Ma'lumot yo'q</p>
            @endif
        </div>
    </div>

    <!-- Guruhlar Statistikasi -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">🏫 Guruhlar Bo'yicha Bugungi Statistika</h3>
        @if(count($guruhlarStatistikasi) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($guruhlarStatistikasi as $stat)
            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-800">{{ $stat['guruh']->nomi }}</h4>
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $stat['foiz'] >= 80 ? 'bg-green-100 text-green-700' : ($stat['foiz'] >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ $stat['foiz'] }}%
                    </span>
                </div>
                <p class="text-xs text-gray-500 mb-2">{{ $stat['talabalar_soni'] }} talaba</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-300
                        {{ $stat['foiz'] >= 80 ? 'bg-green-500' : ($stat['foiz'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                         style="width: {{ $stat['foiz'] }}%"></div>
                </div>
                <div class="flex justify-between mt-2 text-xs text-gray-500">
                    <span class="text-green-600">Bor: {{ $stat['bor'] }}</span>
                    <span class="text-red-600">Yo'q: {{ $stat['yoq'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">Bugun davomat olinmagan</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Trend Chart
const ctx = document.getElementById('trendChart').getContext('2d');
const trendData = @json($kunlikTrend);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: trendData.map(d => d.kun + '\n' + d.sana),
        datasets: [{
            label: 'Davomat %',
            data: trendData.map(d => d.foiz),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 },
                callbacks: {
                    label: function(context) {
                        return 'Davomat: ' + context.parsed.y + '%';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>
@endpush
