@extends('layouts.app')

@section('title', 'Yangi Foydalanuvchi - Kollej Davomat Tizimi')
@section('page-title', 'Yangi Foydalanuvchi Qo\'shish')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <i data-lucide="user-plus" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
                <h2 class="font-semibold text-foreground">Yangi Foydalanuvchi</h2>
                <p class="text-sm text-muted-foreground">Foydalanuvchi ma'lumotlarini kiriting</p>
            </div>
        </div>

        <form action="{{ route('foydalanuvchilar.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Ism -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Ism *</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="input pl-10 @error('name') border-destructive @enderror"
                           placeholder="To'liq ism">
                </div>
                @error('name')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Email *</label>
                <div class="relative">
                    <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="input pl-10 @error('email') border-destructive @enderror"
                           placeholder="email@example.com">
                </div>
                @error('email')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Parol -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Parol *</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="password" name="password"
                           class="input pl-10 @error('password') border-destructive @enderror"
                           placeholder="••••••••">
                </div>
                @error('password')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Parolni tasdiqlash -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Parolni tasdiqlash *</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="password" name="password_confirmation"
                           class="input pl-10"
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Rol *</label>
                <select name="role" class="input @error('role') border-destructive @enderror">
                    <option value="">-- Tanlang --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="davomat_oluvchi" {{ old('role') == 'davomat_oluvchi' ? 'selected' : '' }}>Davomat Oluvchi</option>
                    <option value="koruvchi" {{ old('role') == 'koruvchi' ? 'selected' : '' }}>Ko'ruvchi</option>
                </select>
                @error('role')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-border">
                <a href="{{ route('foydalanuvchilar.index') }}" class="btn btn-outline gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Bekor qilish
                </a>
                <button type="submit" class="btn btn-primary gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Saqlash
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
