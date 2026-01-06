@extends('layouts.app')

@section('title', 'Talabalar - Kollej Davomat Tizimi')
@section('page-title', 'Talabalar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-gray-500">Barcha talabalar ro'yxati</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('talabalar.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Yangi Talaba
        </a>
        @endif
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form action="{{ route('talabalar.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="qidiruv" value="{{ request('qidiruv') }}" placeholder="FISH bo'yicha qidirish..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="guruh_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Barcha guruhlar</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}" {{ request('guruh_id') == $guruh->id ? 'selected' : '' }}>{{ $guruh->nomi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="holat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Barcha holatlar</option>
                    <option value="aktiv" {{ request('holat') == 'aktiv' ? 'selected' : '' }}>Aktiv</option>
                    <option value="noaktiv" {{ request('holat') == 'noaktiv' ? 'selected' : '' }}>Noaktiv</option>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Qidirish
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">FISH</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guruh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kirgan sana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holati</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amallar</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($talabalar as $index => $talaba)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ($talabalar->currentPage() - 1) * $talabalar->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">{{ strtoupper(substr($talaba->fish, 0, 1)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $talaba->fish }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                {{ $talaba->guruh?->nomi ?? 'Guruhsiz' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $talaba->kirgan_sana->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $talaba->holati === 'aktiv' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $talaba->holat_nomi }}
                            </span>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('talabalar.show', $talaba) }}" class="text-gray-600 hover:text-gray-900">Ko'rish</a>
                            <a href="{{ route('talabalar.edit', $talaba) }}" class="text-blue-600 hover:text-blue-900">Tahrirlash</a>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Talabalar topilmadi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $talabalar->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
