@extends('layouts.app')

@section('title', 'Guruhlar - Kollej Davomat Tizimi')
@section('page-title', 'Guruhlar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-gray-500">Barcha guruhlar ro'yxati</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('guruhlar.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Yangi Guruh
        </a>
        @endif
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form action="{{ route('guruhlar.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="qidiruv" value="{{ request('qidiruv') }}" placeholder="Qidirish..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="kurs" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Barcha kurslar</option>
                    @foreach($kurslar as $kurs)
                    <option value="{{ $kurs }}" {{ request('kurs') == $kurs ? 'selected' : '' }}>{{ $kurs }}-kurs</option>
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

    <!-- Guruhlar Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($guruhlar as $guruh)
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $guruh->nomi }}</h3>
                    <p class="text-sm text-gray-500">{{ $guruh->yunalish }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full {{ $guruh->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $guruh->is_active ? 'Aktiv' : 'Noaktiv' }}
                </span>
            </div>

            <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-gray-500">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ $guruh->aktiv_talabalar_count }} talaba
                </span>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                    {{ $guruh->kurs }}-kurs
                </span>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="mt-4 pt-4 border-t flex justify-end space-x-2">
                <a href="{{ route('guruhlar.show', $guruh->id) }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                <a href="{{ route('guruhlar.edit', $guruh->id) }}" class="text-blue-600 hover:text-blue-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                @if($guruh->canBeDeleted())
                <form action="{{ route('guruhlar.destroy', $guruh->id) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan o\'chirmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full bg-gray-50 rounded-xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <p class="text-gray-500">Guruhlar topilmadi</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $guruhlar->withQueryString()->links() }}
    </div>
</div>
@endsection
