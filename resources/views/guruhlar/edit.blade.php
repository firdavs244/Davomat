@extends('layouts.app')

@section('title', 'Guruhni Tahrirlash - Kollej Davomat Tizimi')
@section('page-title', 'Guruhni Tahrirlash')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <i data-lucide="edit" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
                <h2 class="font-semibold text-foreground">{{ $guruh->nomi }}</h2>
                <p class="text-sm text-muted-foreground">Guruh ma'lumotlarini tahrirlash</p>
            </div>
        </div>

        <form action="{{ route('guruhlar.update', $guruh) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Guruh nomi -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Guruh nomi *</label>
                <div class="relative">
                    <i data-lucide="hash" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="text" name="nomi" value="{{ old('nomi', $guruh->nomi) }}"
                           class="input pl-10 @error('nomi') border-destructive @enderror">
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
                <select name="kurs" class="input">
                    <option value="1" {{ old('kurs', $guruh->kurs) == 1 ? 'selected' : '' }}>1-kurs</option>
                    <option value="2" {{ old('kurs', $guruh->kurs) == 2 ? 'selected' : '' }}>2-kurs</option>
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
                    <input type="text" name="yunalish" value="{{ old('yunalish', $guruh->yunalish) }}" list="yunalishlar"
                           class="input pl-10">
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

            <!-- Holat -->
            <div class="p-4 bg-muted/30 rounded-lg border border-border">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $guruh->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-border text-primary focus:ring-primary">
                    <span class="ml-3">
                        <span class="text-sm font-medium text-foreground">Aktiv guruh</span>
                        <span class="block text-xs text-muted-foreground">Noaktiv guruhlar davomat olishda ko'rinmaydi</span>
                    </span>
                </label>
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
