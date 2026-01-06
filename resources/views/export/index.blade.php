@extends('layouts.app')

@section('title', 'Export - Kollej Davomat Tizimi')
@section('page-title', 'Davomat Export')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Export Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📥 Davomat ma'lumotlarini Excel formatda export qilish</h3>

        <form action="{{ route('export.csv') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Guruh tanlash -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guruh *</label>
                <select name="guruh_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('guruh_id') border-red-500 @enderror" required>
                    <option value="">-- Guruh tanlang --</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}">{{ $guruh->nomi }} ({{ $guruh->kurs }}-kurs, {{ $guruh->aktivTalabalar()->count() }} talaba)</option>
                    @endforeach
                </select>
                @error('guruh_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Davr tanlash -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Davr *</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3" x-data="{ davr: 'kunlik' }">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           :class="davr === 'kunlik' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                        <input type="radio" name="davr" value="kunlik" x-model="davr" class="hidden">
                        <span class="text-sm" :class="davr === 'kunlik' ? 'text-blue-700 font-semibold' : 'text-gray-700'">Kunlik</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           :class="davr === 'haftalik' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                        <input type="radio" name="davr" value="haftalik" x-model="davr" class="hidden">
                        <span class="text-sm" :class="davr === 'haftalik' ? 'text-blue-700 font-semibold' : 'text-gray-700'">Haftalik</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           :class="davr === 'oylik' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                        <input type="radio" name="davr" value="oylik" x-model="davr" class="hidden">
                        <span class="text-sm" :class="davr === 'oylik' ? 'text-blue-700 font-semibold' : 'text-gray-700'">Oylik</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           :class="davr === 'yillik' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                        <input type="radio" name="davr" value="yillik" x-model="davr" class="hidden">
                        <span class="text-sm" :class="davr === 'yillik' ? 'text-blue-700 font-semibold' : 'text-gray-700'">Yillik</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                           :class="davr === 'maxsus' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                        <input type="radio" name="davr" value="maxsus" x-model="davr" class="hidden">
                        <span class="text-sm" :class="davr === 'maxsus' ? 'text-blue-700 font-semibold' : 'text-gray-700'">Maxsus</span>
                    </label>

                    <!-- Maxsus sana oralig'i -->
                    <div class="col-span-2 md:col-span-5 mt-4" x-show="davr === 'maxsus'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Boshlanish sanasi</label>
                                <input type="date" name="sana_dan" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tugash sanasi</label>
                                <input type="date" name="sana_gacha" value="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
                @error('davr')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full md:w-auto px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel faylni yuklab olish
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Export Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($guruhlar as $guruh)
        <div class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h4 class="font-semibold text-gray-800">{{ $guruh->nomi }}</h4>
                    <p class="text-xs text-gray-500">{{ $guruh->yunalish }} | {{ $guruh->aktivTalabalar()->count() }} talaba</p>
                </div>
                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">{{ $guruh->kurs }}-kurs</span>
            </div>
            <div class="flex space-x-2">
                <form action="{{ route('export.csv') }}" method="POST" class="flex-1">
                    @csrf
                    <input type="hidden" name="guruh_id" value="{{ $guruh->id }}">
                    <input type="hidden" name="davr" value="oylik">
                    <button type="submit" class="w-full px-3 py-2 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        📊 Oylik
                    </button>
                </form>
                <a href="{{ route('export.guruh', $guruh) }}" class="flex-1">
                    <button type="button" class="w-full px-3 py-2 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                        📈 Hisobot
                    </button>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Info -->
    <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-xl p-6">
        <h4 class="font-semibold text-green-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            Excel fayl haqida ma'lumot
        </h4>
        <ul class="text-sm text-gray-700 space-y-2">
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Professional formatda:</strong> Ranglar, borderlar, fontlar va avtomatik kenglik</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Rang kodlash:</strong> Davomat foizi bo'yicha avtomatik rang (yashil, sariq, qizil)</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>To'liq ma'lumotlar:</strong> Talaba, sana, hafta kuni, paralar, foiz va izohlar</span>
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 mr-2 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><strong>Fayl formati:</strong> .xlsx (Microsoft Excel 2007 va yuqori)</span>
            </li>
        </ul>
    </div>
</div>
@endsection
