@extends('layouts.app')

@section('title', 'Davomatni Tahrirlash - Kollej Davomat Tizimi')
@section('page-title', 'Davomatni Tahrirlash')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-6 py-4 border-b border-border">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                    <span class="text-primary font-bold text-lg">{{ strtoupper(substr($davomat->talaba?->fish ?? 'N', 0, 1)) }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-foreground">{{ $davomat->talaba?->fish ?? 'Noma\'lum talaba' }}</h3>
                    <p class="text-sm text-muted-foreground flex items-center gap-2">
                        <span>{{ $davomat->guruh?->nomi ?? '-' }}</span>
                        <span class="w-1 h-1 rounded-full bg-muted-foreground"></span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            {{ $davomat->sana->format('d.m.Y') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('davomat.update', $davomat) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- 1-para -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-foreground">1-para (08:30 - 09:50)</label>
                <div class="flex flex-wrap gap-3">
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all"
                           :class="para1 === 'bor' ? 'border-success bg-success/10 text-success' : 'border-border hover:bg-muted'"
                           x-data="{ para1: '{{ $davomat->para_1 ?? '' }}' }">
                        <input type="radio" name="para_1" value="bor" {{ $davomat->para_1 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-success focus:ring-success" @change="para1 = 'bor'">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span class="font-medium">Bor</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_1" value="yoq" {{ $davomat->para_1 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-destructive focus:ring-destructive">
                        <i data-lucide="x" class="w-4 h-4 text-destructive"></i>
                        <span class="font-medium text-destructive">Yo'q</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_1" value="" {{ $davomat->para_1 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-muted-foreground focus:ring-muted">
                        <i data-lucide="minus" class="w-4 h-4 text-muted-foreground"></i>
                        <span class="font-medium text-muted-foreground">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- 2-para -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-foreground">2-para (10:00 - 11:20)</label>
                <div class="flex flex-wrap gap-3">
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_2" value="bor" {{ $davomat->para_2 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-success focus:ring-success">
                        <i data-lucide="check" class="w-4 h-4 text-success"></i>
                        <span class="font-medium text-success">Bor</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_2" value="yoq" {{ $davomat->para_2 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-destructive focus:ring-destructive">
                        <i data-lucide="x" class="w-4 h-4 text-destructive"></i>
                        <span class="font-medium text-destructive">Yo'q</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_2" value="" {{ $davomat->para_2 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-muted-foreground focus:ring-muted">
                        <i data-lucide="minus" class="w-4 h-4 text-muted-foreground"></i>
                        <span class="font-medium text-muted-foreground">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- 3-para -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-foreground">3-para (11:30 - 12:50)</label>
                <div class="flex flex-wrap gap-3">
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_3" value="bor" {{ $davomat->para_3 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-success focus:ring-success">
                        <i data-lucide="check" class="w-4 h-4 text-success"></i>
                        <span class="font-medium text-success">Bor</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_3" value="yoq" {{ $davomat->para_3 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-destructive focus:ring-destructive">
                        <i data-lucide="x" class="w-4 h-4 text-destructive"></i>
                        <span class="font-medium text-destructive">Yo'q</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_3" value="" {{ $davomat->para_3 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-muted-foreground focus:ring-muted">
                        <i data-lucide="minus" class="w-4 h-4 text-muted-foreground"></i>
                        <span class="font-medium text-muted-foreground">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- 4-para -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-foreground">4-para (13:30 - 14:50)</label>
                <div class="flex flex-wrap gap-3">
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_4" value="bor" {{ $davomat->para_4 === 'bor' ? 'checked' : '' }}
                               class="w-4 h-4 text-success focus:ring-success">
                        <i data-lucide="check" class="w-4 h-4 text-success"></i>
                        <span class="font-medium text-success">Bor</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_4" value="yoq" {{ $davomat->para_4 === 'yoq' ? 'checked' : '' }}
                               class="w-4 h-4 text-destructive focus:ring-destructive">
                        <i data-lucide="x" class="w-4 h-4 text-destructive"></i>
                        <span class="font-medium text-destructive">Yo'q</span>
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-all border-border hover:bg-muted">
                        <input type="radio" name="para_4" value="" {{ $davomat->para_4 === null ? 'checked' : '' }}
                               class="w-4 h-4 text-muted-foreground focus:ring-muted">
                        <i data-lucide="minus" class="w-4 h-4 text-muted-foreground"></i>
                        <span class="font-medium text-muted-foreground">Belgilanmagan</span>
                    </label>
                </div>
            </div>

            <!-- Izoh -->
            <div class="space-y-2">
                <label class="block text-sm font-medium text-foreground flex items-center gap-1">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    Izoh
                </label>
                <textarea name="izoh" rows="3" class="input resize-none" placeholder="Qo'shimcha izoh yozing...">{{ old('izoh', $davomat->izoh) }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t border-border">
                <a href="{{ route('davomat.tarixi') }}" class="btn btn-outline gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
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
