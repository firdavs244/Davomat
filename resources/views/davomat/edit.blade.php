@extends('layouts.app')

@section('title', 'Davomatni Tahrirlash - Kollej Davomat Tizimi')
@section('page-title', 'Davomatni Tahrirlash')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Header -->
        <div class="mb-6 pb-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">{{ $davomat->talaba?->fish ?? 'Noma\'lum talaba' }}</h3>
            <p class="text-sm text-gray-500">{{ $davomat->guruh?->nomi ?? '-' }} | {{ $davomat->sana->format('d.m.Y') }}</p>
        </div>

        <form action="{{ route('davomat.update', $davomat) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1-para -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">1-para</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="para_1" value="bor" {{ $davomat->para_1 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-green-600">Bor</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="para_1" value="yoq" {{ $davomat->para_1 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-red-600">Yo'q</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="para_1" value="" {{ $davomat->para_1 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-gray-600 focus:ring-gray-500">
                        <span class="ml-2 text-gray-600">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- 2-para -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">2-para</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="para_2" value="bor" {{ $davomat->para_2 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-green-600">Bor</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="para_2" value="yoq" {{ $davomat->para_2 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-red-600">Yo'q</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="para_2" value="" {{ $davomat->para_2 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-gray-600 focus:ring-gray-500">
                        <span class="ml-2 text-gray-600">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- 3-para -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">3-para</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="para_3" value="bor" {{ $davomat->para_3 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-green-600">Bor</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="para_3" value="yoq" {{ $davomat->para_3 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-red-600">Yo'q</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="para_3" value="" {{ $davomat->para_3 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-gray-600 focus:ring-gray-500">
                        <span class="ml-2 text-gray-600">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- Izoh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Izoh</label>
                <textarea name="izoh" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('izoh', $davomat->izoh) }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('davomat.tarixi') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Bekor qilish
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Saqlash
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
