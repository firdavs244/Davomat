@extends('layouts.app')

@section('title', 'Yangi Guruh - Kollej Davomat Tizimi')
@section('page-title', 'Yangi Guruh Qo\'shish')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('guruhlar.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Guruh nomi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guruh nomi *</label>
                <input type="text" name="nomi" value="{{ old('nomi') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('nomi') border-red-500 @enderror"
                       placeholder="Masalan: IT-101">
                @error('nomi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Kurs -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kurs *</label>
                <select name="kurs" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('kurs') border-red-500 @enderror">
                    <option value="">-- Tanlang --</option>
                    <option value="1" {{ old('kurs') == 1 ? 'selected' : '' }}>1-kurs</option>
                    <option value="2" {{ old('kurs') == 2 ? 'selected' : '' }}>2-kurs</option>
                    <option value="3" {{ old('kurs') == 3 ? 'selected' : '' }}>3-kurs</option>
                    <option value="4" {{ old('kurs') == 4 ? 'selected' : '' }}>4-kurs</option>
                </select>
                @error('kurs')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Yo'nalish -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yo'nalish *</label>
                <input type="text" name="yunalish" value="{{ old('yunalish') }}" list="yunalishlar"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('yunalish') border-red-500 @enderror"
                       placeholder="Masalan: Dasturlash">
                <datalist id="yunalishlar">
                    @foreach($yunalishlar as $y)
                    <option value="{{ $y }}">
                    @endforeach
                </datalist>
                @error('yunalish')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('guruhlar.index') }}" 
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
