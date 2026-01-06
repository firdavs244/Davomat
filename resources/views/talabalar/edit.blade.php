@extends('layouts.app')

@section('title', 'Talabani Tahrirlash - Kollej Davomat Tizimi')
@section('page-title', 'Talabani Tahrirlash')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('talabalar.update', $talaba) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- FISH -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Familiya Ism Sharif *</label>
                <input type="text" name="fish" value="{{ old('fish', $talaba->fish) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('fish') border-red-500 @enderror">
                @error('fish')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Guruh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guruh *</label>
                <select name="guruh_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}" {{ old('guruh_id', $talaba->guruh_id) == $guruh->id ? 'selected' : '' }}>
                        {{ $guruh->nomi }} ({{ $guruh->kurs }}-kurs, {{ $guruh->yunalish }})
                    </option>
                    @endforeach
                </select>
                @error('guruh_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Kirgan sana -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kirgan sana *</label>
                <input type="date" name="kirgan_sana" value="{{ old('kirgan_sana', $talaba->kirgan_sana->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('kirgan_sana')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Ketgan sana -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ketgan sana</label>
                <input type="date" name="ketgan_sana" value="{{ old('ketgan_sana', $talaba->ketgan_sana?->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">Talaba ketgan bo'lsa sanasini kiriting</p>
                @error('ketgan_sana')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Holati -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Holati *</label>
                <select name="holati" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="aktiv" {{ old('holati', $talaba->holati) === 'aktiv' ? 'selected' : '' }}>Aktiv</option>
                    <option value="noaktiv" {{ old('holati', $talaba->holati) === 'noaktiv' ? 'selected' : '' }}>Noaktiv</option>
                </select>
            </div>
            
            <!-- Izoh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Izoh</label>
                <textarea name="izoh" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('izoh', $talaba->izoh) }}</textarea>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('talabalar.index') }}" 
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
