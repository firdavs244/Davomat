@extends('layouts.app')

@section('title', 'Foydalanuvchini Tahrirlash - Kollej Davomat Tizimi')
@section('page-title', 'Foydalanuvchini Tahrirlash')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('foydalanuvchilar.update', $foydalanuvchi) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Ism -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To'liq ism *</label>
                <input type="text" name="name" value="{{ old('name', $foydalanuvchi->name) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $foydalanuvchi->email) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Rol -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol *</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="koruvchi" {{ old('role', $foydalanuvchi->role) == 'koruvchi' ? 'selected' : '' }}>Ko'ruvchi</option>
                    <option value="davomat_oluvchi" {{ old('role', $foydalanuvchi->role) == 'davomat_oluvchi' ? 'selected' : '' }}>Davomat Oluvchi</option>
                    <option value="admin" {{ old('role', $foydalanuvchi->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
                @error('role')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Holat -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $foydalanuvchi->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Aktiv</span>
                </label>
            </div>
            
            <!-- Parol -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Yangi parol</label>
                <input type="password" name="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                       placeholder="Bo'sh qoldiring agar o'zgartirmoqchi bo'lmasangiz">
                <p class="mt-1 text-xs text-gray-500">Parolni o'zgartirish ixtiyoriy</p>
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Parolni tasdiqlash -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parolni tasdiqlash</label>
                <input type="password" name="password_confirmation" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('foydalanuvchilar.index') }}" 
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
