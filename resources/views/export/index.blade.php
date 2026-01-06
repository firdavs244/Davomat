@extends('layouts.app')

@section('title', 'Export - Kollej Davomat Tizimi')
@section('page-title', 'Davomat Export')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Export Form -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-border">
            <div class="w-10 h-10 rounded-lg bg-success/10 flex items-center justify-center">
                <i data-lucide="file-spreadsheet" class="w-5 h-5 text-success"></i>
            </div>
            <div>
                <h3 class="font-semibold text-foreground">Davomat Export</h3>
                <p class="text-sm text-muted-foreground">Excel formatda yuklab olish</p>
            </div>
        </div>

        <form action="{{ route('export.csv') }}" method="POST" class="space-y-6" x-data="{ davr: 'kunlik' }">
            @csrf

            <!-- Guruh tanlash -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Guruh *</label>
                <select name="guruh_id" class="input @error('guruh_id') border-destructive @enderror" required>
                    <option value="">-- Guruh tanlang --</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}">{{ $guruh->nomi }} ({{ $guruh->kurs }}-kurs, {{ $guruh->aktivTalabalar()->count() }} talaba)</option>
                    @endforeach
                </select>
                @error('guruh_id')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Davr tanlash -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-3">Davr *</label>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-all"
                           :class="davr === 'kunlik' ? 'border-primary bg-primary/10 text-primary' : 'border-border hover:bg-muted text-muted-foreground'">
                        <input type="radio" name="davr" value="kunlik" x-model="davr" class="hidden">
                        <div class="text-center">
                            <i data-lucide="calendar" class="w-5 h-5 mx-auto mb-1"></i>
                            <span class="text-sm font-medium">Kunlik</span>
                        </div>
                    </label>
                    <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-all"
                           :class="davr === 'haftalik' ? 'border-primary bg-primary/10 text-primary' : 'border-border hover:bg-muted text-muted-foreground'">
                        <input type="radio" name="davr" value="haftalik" x-model="davr" class="hidden">
                        <div class="text-center">
                            <i data-lucide="calendar-days" class="w-5 h-5 mx-auto mb-1"></i>
                            <span class="text-sm font-medium">Haftalik</span>
                        </div>
                    </label>
                    <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-all"
                           :class="davr === 'oylik' ? 'border-primary bg-primary/10 text-primary' : 'border-border hover:bg-muted text-muted-foreground'">
                        <input type="radio" name="davr" value="oylik" x-model="davr" class="hidden">
                        <div class="text-center">
                            <i data-lucide="calendar-range" class="w-5 h-5 mx-auto mb-1"></i>
                            <span class="text-sm font-medium">Oylik</span>
                        </div>
                    </label>
                    <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-all"
                           :class="davr === 'yillik' ? 'border-primary bg-primary/10 text-primary' : 'border-border hover:bg-muted text-muted-foreground'">
                        <input type="radio" name="davr" value="yillik" x-model="davr" class="hidden">
                        <div class="text-center">
                            <i data-lucide="calendar-check" class="w-5 h-5 mx-auto mb-1"></i>
                            <span class="text-sm font-medium">Yillik</span>
                        </div>
                    </label>
                    <label class="flex items-center justify-center p-3 border rounded-lg cursor-pointer transition-all"
                           :class="davr === 'maxsus' ? 'border-primary bg-primary/10 text-primary' : 'border-border hover:bg-muted text-muted-foreground'">
                        <input type="radio" name="davr" value="maxsus" x-model="davr" class="hidden">
                        <div class="text-center">
                            <i data-lucide="sliders" class="w-5 h-5 mx-auto mb-1"></i>
                            <span class="text-sm font-medium">Maxsus</span>
                        </div>
                    </label>
                </div>

                <!-- Maxsus sana oralig'i -->
                <div class="mt-4" x-show="davr === 'maxsus'" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-muted/30 rounded-lg border border-border">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">Boshlanish sanasi</label>
                            <div class="relative">
                                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                                <input type="date" name="sana_dan" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="input pl-10">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1">Tugash sanasi</label>
                            <div class="relative">
                                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                                <input type="date" name="sana_gacha" value="{{ now()->format('Y-m-d') }}" class="input pl-10">
                            </div>
                        </div>
                    </div>
                </div>
                @error('davr')
                <p class="mt-1 text-sm text-destructive flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    {{ $message }}
                </p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-border">
                <button type="submit" class="btn btn-success gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Excel faylni yuklab olish
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Export Cards -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="zap" class="w-5 h-5 text-accent"></i>
            <h3 class="font-semibold text-foreground">Tezkor Export</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($guruhlar as $guruh)
            <div class="bg-muted/30 rounded-xl border border-border p-4 hover-lift transition-all">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-semibold text-foreground">{{ $guruh->nomi }}</h4>
                        <p class="text-xs text-muted-foreground">{{ $guruh->yunalish }} | {{ $guruh->aktivTalabalar()->count() }} talaba</p>
                    </div>
                    <span class="badge badge-primary">{{ $guruh->kurs }}-kurs</span>
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('export.csv') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="guruh_id" value="{{ $guruh->id }}">
                        <input type="hidden" name="davr" value="oylik">
                        <button type="submit" class="btn btn-outline w-full gap-1 text-xs py-2">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                            Oylik
                        </button>
                    </form>
                    <a href="{{ route('export.guruh', $guruh) }}" class="flex-1">
                        <button type="button" class="btn btn-primary w-full gap-1 text-xs py-2">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            Hisobot
                        </button>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Info -->
    <div class="bg-gradient-to-r from-success/10 to-primary/10 border border-success/30 rounded-xl p-6">
        <h4 class="font-semibold text-foreground mb-4 flex items-center gap-2">
            <i data-lucide="info" class="w-5 h-5 text-success"></i>
            Excel fayl haqida ma'lumot
        </h4>
        <ul class="text-sm text-muted-foreground space-y-3">
            <li class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-success flex-shrink-0 mt-0.5"></i>
                <span><strong class="text-foreground">Professional formatda:</strong> Ranglar, borderlar, fontlar va avtomatik kenglik</span>
            </li>
            <li class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-success flex-shrink-0 mt-0.5"></i>
                <span><strong class="text-foreground">Rang kodlash:</strong> Davomat foizi bo'yicha avtomatik rang (yashil, sariq, qizil)</span>
            </li>
            <li class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-success flex-shrink-0 mt-0.5"></i>
                <span><strong class="text-foreground">To'liq ma'lumotlar:</strong> Talaba, sana, hafta kuni, paralar, foiz va izohlar</span>
            </li>
            <li class="flex items-start gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-success flex-shrink-0 mt-0.5"></i>
                <span><strong class="text-foreground">Statistika:</strong> Har bir talaba uchun umumiy davomat foizi</span>
            </li>
        </ul>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
