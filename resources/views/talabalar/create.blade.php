@extends('layouts.app')

@section('title', 'Yangi Talaba - Kollej Davomat Tizimi')
@section('page-title', 'Yangi Talaba Qo\'shish')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border">
            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                <i data-lucide="user-plus" class="w-5 h-5 text-primary"></i>
            </div>
            <div>
                <h2 class="font-semibold text-foreground">Yangi Talaba</h2>
                <p class="text-sm text-muted-foreground">Talaba ma'lumotlarini kiriting</p>
            </div>
        </div>

        <form action="{{ route('talabalar.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- FISH -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">F.I.SH *</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="text" name="fish" value="{{ old('fish') }}"
                           class="input pl-10 @error('fish') border-destructive @enderror"
                           placeholder="Familiya Ism Sharif">
                </div>
                @error('fish')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Guruh -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Guruh *</label>
                <select name="guruh_id" class="input @error('guruh_id') border-destructive @enderror">
                    <option value="">-- Tanlang --</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}" {{ old('guruh_id') == $guruh->id ? 'selected' : '' }}>{{ $guruh->nomi }}</option>
                    @endforeach
                </select>
                @error('guruh_id')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Kirgan sana -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Kirgan sana *</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="date" name="kirgan_sana" value="{{ old('kirgan_sana', date('Y-m-d')) }}"
                           class="input pl-10 @error('kirgan_sana') border-destructive @enderror">
                </div>
                @error('kirgan_sana')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-border">
                <a href="{{ route('talabalar.index') }}" class="btn btn-outline gap-2">
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
