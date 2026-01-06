@extends('layouts.app')

@section('title', $guruh->nomi . ' Hisoboti - Kollej Davomat Tizimi')
@section('page-title', $guruh->nomi . ' - Oylik Hisobot')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $guruh->nomi }}</h2>
                <p class="text-gray-500">{{ $guruh->yunalish }} | {{ $guruh->kurs }}-kurs</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-col sm:flex-row gap-2">
                <form action="{{ route('export.guruh', $guruh) }}" method="GET" class="flex space-x-2">
                    <select name="oy" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $oy == $i ? 'selected' : '' }}>{{ $i }}-oy</option>
                        @endfor
                    </select>
                    <select name="yil" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $yil == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Ko'rsatish
                    </button>
                </form>
                <a href="{{ route('export.guruh', ['guruh' => $guruh, 'oy' => $oy, 'yil' => $yil, 'export' => 1]) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel yuklash
                </a>
            </div>
        </div>
    </div>

    <!-- Statistika Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-800">{{ $yil }}-yil {{ $oy }}-oy davomati</h3>
            <p class="text-sm text-gray-600 mt-1">Talabalar davomat statistikasi va foizlar</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">FISH</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Yo'q</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jami</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Foiz</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hisobot as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item['talaba']->fish }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600 font-semibold">
                            {{ $item['bor'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600 font-semibold">
                            {{ $item['yoq'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700 font-medium">
                            {{ $item['bor'] + $item['yoq'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 text-xs rounded-full font-bold
                                {{ $item['foiz'] >= 90 ? 'bg-green-100 text-green-800' : ($item['foiz'] >= 70 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $item['foiz'] }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Bu oy uchun davomat ma'lumotlari yo'q
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('export.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Export sahifasiga qaytish
        </a>
    </div>
</div>
@endsection
