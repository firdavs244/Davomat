@extends('layouts.app')

@section('title', $talaba->fish . ' - Kollej Davomat Tizimi')
@section('page-title', 'Talaba ma\'lumotlari')

@section('content')
<div class="space-y-6">
    <!-- Talaba Info Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-blue-600">{{ strtoupper(substr($talaba->fish, 0, 1)) }}</span>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $talaba->fish }}</h2>
                    @if($talaba->guruh)
                    <p class="text-gray-500">{{ $talaba->guruh->nomi }} | {{ $talaba->guruh->yunalish }}</p>
                    @else
                    <p class="text-gray-500 italic">Guruhsiz</p>
                    @endif
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <span class="px-3 py-1 rounded-full text-sm {{ $talaba->holati === 'aktiv' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $talaba->holat_nomi }}
                </span>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('talabalar.edit', $talaba) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Tahrirlash
                </a>
                @endif
            </div>
        </div>

        <!-- Ma'lumotlar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 pt-6 border-t">
            <div>
                <p class="text-sm text-gray-500">Kirgan sana</p>
                <p class="font-semibold text-gray-800">{{ $talaba->kirgan_sana->format('d.m.Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ketgan sana</p>
                <p class="font-semibold text-gray-800">{{ $talaba->ketgan_sana ? $talaba->ketgan_sana->format('d.m.Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Guruh</p>
                <p class="font-semibold text-gray-800">{{ $talaba->guruh?->nomi ?? 'Guruhsiz' }}</p>
            </div>
        </div>

        @if($talaba->izoh)
        <div class="mt-4 pt-4 border-t">
            <p class="text-sm text-gray-500">Izoh</p>
            <p class="text-gray-800">{{ $talaba->izoh }}</p>
        </div>
        @endif
    </div>

    <!-- Statistika -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $statistika['jami_paralar'] }}</p>
            <p class="text-sm text-gray-500">Jami paralar</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $statistika['yoq_paralar'] }}</p>
            <p class="text-sm text-gray-500">Yo'qliklar</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            <p class="text-3xl font-bold {{ $statistika['yoqlik_foizi'] > 20 ? 'text-red-600' : 'text-green-600' }}">{{ $statistika['yoqlik_foizi'] }}%</p>
            <p class="text-sm text-gray-500">Yo'qlik foizi</p>
        </div>
    </div>

    <!-- Oxirgi davomatlar -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Oxirgi 30 kunlik davomat</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sana</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">1-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">2-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">3-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jami yo'q</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($talaba->davomatlar as $davomat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $davomat->sana->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_1 === 'bor')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Bor</span>
                            @elseif($davomat->para_1 === 'yoq')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Yo'q</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_2 === 'bor')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Bor</span>
                            @elseif($davomat->para_2 === 'yoq')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Yo'q</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_3 === 'bor')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Bor</span>
                            @elseif($davomat->para_3 === 'yoq')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Yo'q</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="font-semibold {{ $davomat->jami_yoq > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                {{ $davomat->jami_yoq > 0 ? $davomat->jami_yoq : '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Davomat ma'lumotlari yo'q
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
