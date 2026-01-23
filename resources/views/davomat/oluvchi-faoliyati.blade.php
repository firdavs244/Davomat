@extends('layouts.app')

@section('title', $user->name . ' - Faoliyat tarixi')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('davomat.hisobot') }}"
                class="p-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                <p class="text-gray-500">Davomat olish faoliyati tarixi</p>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Dan:</label>
                <input type="date" name="boshlanish" value="{{ $boshlanish }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Gacha:</label>
                <input type="date" name="tugash" value="{{ $tugash }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    max="{{ date('Y-m-d') }}">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-lucide="search" class="w-4 h-4 inline mr-1"></i>
                Qidirish
            </button>
        </form>
    </div>

    {{-- Statistika --}}
    @php
        $jamiGuruhlar = 0;
        $jamiKunlar = 0;
        foreach ($kunlar as $sana => $kunData) {
            if ($kunData['jami'] > 0) {
                $jamiKunlar++;
                $jamiGuruhlar += $kunData['jami'];
            }
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i data-lucide="calendar-days" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ count($kunlar) }}</div>
                    <div class="text-sm text-gray-500">Jami kunlar</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $jamiKunlar }}</div>
                    <div class="text-sm text-gray-500">Faol kunlar</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">{{ $jamiGuruhlar }}</div>
                    <div class="text-sm text-gray-500">Jami guruhlar</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">
                        {{ $jamiKunlar > 0 ? round($jamiGuruhlar / $jamiKunlar, 1) : 0 }}
                    </div>
                    <div class="text-sm text-gray-500">O'rtacha/kun</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kunlik tarix jadvali --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-800">Kunlik faoliyat</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sana</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">1-Para</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">2-Para</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">3-Para</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">4-Para</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jami</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse (array_reverse($kunlar, true) as $sana => $kunData)
                        <tr class="hover:bg-gray-50 {{ $kunData['jami'] > 0 ? '' : 'opacity-50' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($sana)->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($sana)->locale('uz')->dayName }}
                                </div>
                            </td>
                            @foreach ([1, 2, 3, 4] as $para)
                                <td class="px-4 py-3 text-center">
                                    @if($kunData['paralar'][$para]['guruhlar_soni'] > 0)
                                        <div class="inline-flex flex-col items-center">
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                                {{ $kunData['paralar'][$para]['guruhlar_soni'] }} guruh
                                            </span>
                                            <div class="text-xs text-gray-400 mt-1 max-w-32 truncate"
                                                title="{{ implode(', ', $kunData['paralar'][$para]['guruhlar']) }}">
                                                {{ implode(', ', array_slice($kunData['paralar'][$para]['guruhlar'], 0, 2)) }}
                                                @if(count($kunData['paralar'][$para]['guruhlar']) > 2)
                                                    +{{ count($kunData['paralar'][$para]['guruhlar']) - 2 }}
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold {{ $kunData['jami'] > 0 ? 'text-indigo-600' : 'text-gray-400' }}">
                                    {{ $kunData['jami'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                                <p>Bu davrda faoliyat topilmadi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
