@extends('layouts.app')

@section('title', $talaba->fish . ' - Kollej Davomat Tizimi')
@section('page-title', $talaba->fish)

@section('content')
<div class="space-y-6">
    <!-- Talaba Info Card -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <span class="text-2xl font-bold text-primary">{{ strtoupper(substr($talaba->fish, 0, 1)) }}</span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-foreground">{{ $talaba->fish }}</h2>
                    <p class="text-muted-foreground flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        {{ $talaba->guruh?->nomi ?? 'Guruhsiz' }} | {{ $talaba->guruh?->yunalish ?? '' }}
                    </p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <span class="badge {{ $talaba->holati === 'aktiv' ? 'badge-success' : 'badge-muted' }} text-sm px-3 py-1">
                    {{ $talaba->holat_nomi }}
                </span>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('talabalar.edit', $talaba->id) }}" class="btn btn-primary gap-2">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Tahrirlash
                </a>
                @endif
            </div>
        </div>

        <!-- Ma'lumotlar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="bg-muted/30 rounded-xl p-4 border border-border">
                <div class="flex items-center gap-3">
                    <i data-lucide="calendar" class="w-5 h-5 text-primary"></i>
                    <div>
                        <p class="text-sm text-muted-foreground">Kirgan sana</p>
                        <p class="font-semibold text-foreground">{{ $talaba->kirgan_sana->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-muted/30 rounded-xl p-4 border border-border">
                <div class="flex items-center gap-3">
                    <i data-lucide="book-open" class="w-5 h-5 text-secondary"></i>
                    <div>
                        <p class="text-sm text-muted-foreground">Kurs</p>
                        <p class="font-semibold text-foreground">{{ $talaba->guruh?->kurs ?? '-' }}-kurs</p>
                    </div>
                </div>
            </div>
            <div class="bg-muted/30 rounded-xl p-4 border border-border">
                <div class="flex items-center gap-3">
                    <i data-lucide="clock" class="w-5 h-5 text-accent"></i>
                    <div>
                        <p class="text-sm text-muted-foreground">O'qish davomiyligi</p>
                        <p class="font-semibold text-foreground">{{ $talaba->kirgan_sana->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Davomat Statistikasi -->
    @if(isset($davomatStatistika))
    <div class="bg-card rounded-xl border border-border p-6">
        <h3 class="text-lg font-semibold text-foreground mb-4 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-5 h-5 text-primary"></i>
            Davomat Statistikasi
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-success/10 rounded-xl p-4 text-center border border-success/20">
                <p class="text-3xl font-bold text-success">{{ $davomatStatistika['bor'] ?? 0 }}</p>
                <p class="text-sm text-success/80">Bor</p>
            </div>
            <div class="bg-destructive/10 rounded-xl p-4 text-center border border-destructive/20">
                <p class="text-3xl font-bold text-destructive">{{ $davomatStatistika['yoq'] ?? 0 }}</p>
                <p class="text-sm text-destructive/80">Yo'q</p>
            </div>
            <div class="bg-warning/10 rounded-xl p-4 text-center border border-warning/20">
                <p class="text-3xl font-bold text-warning">{{ $davomatStatistika['sababli'] ?? 0 }}</p>
                <p class="text-sm text-warning/80">Sababli</p>
            </div>
            <div class="bg-primary/10 rounded-xl p-4 text-center border border-primary/20">
                <p class="text-3xl font-bold text-primary">{{ $davomatStatistika['foiz'] ?? 0 }}%</p>
                <p class="text-sm text-primary/80">Foiz</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Orqaga tugma -->
    <div class="flex justify-start">
        <a href="{{ route('talabalar.index') }}" class="btn btn-outline gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Orqaga
        </a>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
