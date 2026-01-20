@extends('layouts.app')

@section('title', 'Yangi Guruh - Kollej Davomat Tizimi')
@section('page-title', 'Yangi Guruh Qo\'shish')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
                <h2 class="font-semibold text-foreground">Yangi Guruh</h2>
                <p class="text-sm text-muted-foreground">Guruh ma'lumotlarini kiriting</p>
            </div>
        </div>

        <form action="{{ route('guruhlar.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Guruh nomi -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Guruh nomi *</label>
                <div class="relative">
                    <i data-lucide="hash" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="text" name="nomi" value="{{ old('nomi') }}"
                           class="input pl-10 @error('nomi') border-destructive @enderror"
                           placeholder="Masalan: IT-101">
                </div>
                @error('nomi')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Kurs -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kurs *</label>
                <select name="kurs" class="input @error('kurs') border-destructive @enderror">
                    <option value="">-- Tanlang --</option>
                    <option value="1" {{ old('kurs') == 1 ? 'selected' : '' }}>1-kurs</option>
                    <option value="2" {{ old('kurs') == 2 ? 'selected' : '' }}>2-kurs</option>
                </select>
                @error('kurs')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Yo'nalish -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Yo'nalish *</label>
                <div class="relative">
                    <i data-lucide="graduation-cap" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="text" name="yunalish" value="{{ old('yunalish') }}" list="yunalishlar"
                           class="input pl-10 @error('yunalish') border-destructive @enderror"
                           placeholder="Masalan: Dasturlash">
                </div>
                <datalist id="yunalishlar">
                    @foreach($yunalishlar as $y)
                    <option value="{{ $y }}">
                    @endforeach
                </datalist>
                @error('yunalish')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-border">
                <a href="{{ route('guruhlar.index') }}" class="btn btn-outline gap-2">
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
