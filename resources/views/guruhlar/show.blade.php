@extends('layouts.app')

@section('title', $guruh->nomi . ' - Kollej Davomat Tizimi')
@section('page-title', $guruh->nomi)

@section('content')
<div class="space-y-6">
    <!-- Guruh Info Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $guruh->nomi }}</h2>
                <p class="text-gray-500">{{ $guruh->yunalish }} | {{ $guruh->kurs }}-kurs</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <span class="px-3 py-1 rounded-full text-sm {{ $guruh->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $guruh->is_active ? 'Aktiv' : 'Noaktiv' }}
                </span>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('guruhlar.edit', $guruh->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Tahrirlash
                </a>
                @endif
            </div>
        </div>

        <!-- Statistika -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $guruh->talabalar->where('holati', 'aktiv')->count() }}</p>
                <p class="text-sm text-blue-700">Aktiv talabalar</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-600">{{ $guruh->talabalar->where('holati', 'noaktiv')->count() }}</p>
                <p class="text-sm text-gray-700">Noaktiv talabalar</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $guruh->bugungi_statistika['foiz'] }}%</p>
                <p class="text-sm text-green-700">Bugungi davomat</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-purple-600">{{ $guruh->talabalar->count() }}</p>
                <p class="text-sm text-purple-700">Jami talabalar</p>
            </div>
        </div>
    </div>

    <!-- Talabalar ro'yxati -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Talabalar ro'yxati</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">FISH</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kirgan sana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holati</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amallar</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($guruh->talabalar as $index => $talaba)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $talaba->fish }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $talaba->kirgan_sana->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $talaba->holati === 'aktiv' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $talaba->holat_nomi }}
                            </span>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('talabalar.show', $talaba) }}" class="text-gray-600 hover:text-gray-900 mr-2">Ko'rish</a>
                            <a href="{{ route('talabalar.edit', $talaba) }}" class="text-blue-600 hover:text-blue-900">Tahrirlash</a>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Bu guruhda talabalar yo'q
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
