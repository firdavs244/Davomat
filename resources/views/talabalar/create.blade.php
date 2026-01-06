@extends('layouts.app')

@section('title', 'Yangi Talaba - Kollej Davomat Tizimi')
@section('page-title', 'Yangi Talaba Qo\'shish')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('talabalar.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- FISH -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Familiya Ism Sharif *</label>
                <input type="text" name="fish" value="{{ old('fish') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('fish') border-red-500 @enderror"
                       placeholder="Masalan: Aliyev Ali Valiyevich">
                @error('fish')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Guruh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guruh *</label>
                <select name="guruh_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('guruh_id') border-red-500 @enderror">
                    <option value="">-- Guruh tanlang --</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}" {{ old('guruh_id') == $guruh->id ? 'selected' : '' }}>
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
                <input type="date" name="kirgan_sana" value="{{ old('kirgan_sana', now()->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('kirgan_sana') border-red-500 @enderror">
                @error('kirgan_sana')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Izoh -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Izoh</label>
                <textarea name="izoh" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Qo'shimcha ma'lumotlar...">{{ old('izoh') }}</textarea>
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
