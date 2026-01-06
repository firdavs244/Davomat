@extends('layouts.app')

@section('title', 'Guruhni Tahrirlash - Kollej Davomat Tizimi')
@section('page-title', 'Guruhni Tahrirlash')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('guruhlar.update', $guruh) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Guruh nomi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Guruh nomi *</label>
                <input type="text" name="nomi" value="{{ old('nomi', $guruh->nomi) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('nomi') border-red-500 @enderror">
                @error('nomi')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Kurs -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kurs *</label>
                <select name="kurs" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('kurs', $guruh->kurs) == 1 ? 'selected' : '' }}>1-kurs</option>
                    <option value="2" {{ old('kurs', $guruh->kurs) == 2 ? 'selected' : '' }}>2-kurs</option>
                    <option value="3" {{ old('kurs', $guruh->kurs) == 3 ? 'selected' : '' }}>3-kurs</option>
                    <option value="4" {{ old('kurs', $guruh->kurs) == 4 ? 'selected' : '' }}>4-kurs</option>
                </select>
                @error('kurs')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Yo'nalish -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yo'nalish *</label>
                <input type="text" name="yunalish" value="{{ old('yunalish', $guruh->yunalish) }}" list="yunalishlar"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <datalist id="yunalishlar">
                    @foreach($yunalishlar as $y)
                    <option value="{{ $y }}">
                    @endforeach
                </datalist>
                @error('yunalish')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Holat -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $guruh->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Aktiv</span>
                </label>
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
