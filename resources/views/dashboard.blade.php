@extends('layouts.app')

@section('title', 'Dashboard - Kollej Davomat Tizimi')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="dashboardApp()">

    {{-- ===== HEADER: Vaqt va Umumiy Ma'lumot ===== --}}
    <div class="bg-gradient-to-r from-primary/10 via-secondary/10 to-accent/10 rounded-2xl border border-border p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- Salom va Sana --}}
            <div>
                <h1 class="text-2xl font-bold text-foreground flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-primary"></i>
                    </div>
                    Xush kelibsiz, {{ auth()->user()->name }}!
                </h1>
                <p class="text-muted-foreground mt-1 ml-13">
                    {{ now()->locale('uz')->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>

            {{-- Vaqt va Para Holati --}}
            <div class="flex items-center gap-6">
                {{-- Server Vaqti --}}
                <div class="text-center">
                    <p class="text-xs text-muted-foreground uppercase tracking-wide">Hozirgi vaqt</p>
                    <p class="text-3xl font-bold text-primary font-mono" id="serverTime">{{ $paraHolati['hozirgi_vaqt'] }}</p>
                </div>

                <div class="h-12 w-px bg-border"></div>

                {{-- Hozirgi Para --}}
                <div class="text-center">
                    @if($paraHolati['davomat_para'])
                        <p class="text-xs text-muted-foreground uppercase tracking-wide">Hozirgi Para</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-success"></span>
                            </span>
                            <span class="text-2xl font-bold text-success">{{ $paraHolati['davomat_para'] }}-para</span>
                        </div>
                    @elseif($paraHolati['kun_tugadi'])
                        <p class="text-xs text-muted-foreground uppercase tracking-wide">Holat</p>
                        <p class="text-lg font-semibold text-muted-foreground mt-1">Darslar tugadi</p>
                    @else
                        <p class="text-xs text-muted-foreground uppercase tracking-wide">Keyingi Para</p>
                        <p class="text-lg font-semibold text-warning mt-1">{{ $paraHolati['keyingi_boshlanish'] ?? '--:--' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== UMUMIY STATISTIKA KARTALAR ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Jami Guruhlar --}}
        <div class="bg-card rounded-xl border border-border p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Jami Guruhlar</p>
                    <p class="text-3xl font-bold text-foreground mt-1">{{ $jamiGuruhlar }}</p>
                    <p class="text-xs text-primary mt-2">1 va 2-kurs</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-primary"></i>
                </div>
            </div>
        </div>

        {{-- Jami Talabalar --}}
        <div class="bg-card rounded-xl border border-border p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Aktiv Talabalar</p>
                    <p class="text-3xl font-bold text-foreground mt-1">{{ $jamiTalabalar }}</p>
                    <p class="text-xs text-success mt-2">O'qiyotganlar</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-success"></i>
                </div>
            </div>
        </div>

        {{-- Bugungi Bor --}}
        <div class="bg-card rounded-xl border border-border p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Bugun Keldi</p>
                    <p class="text-3xl font-bold text-success mt-1" id="bugunBor">{{ $bugungiUmumiy['bor'] }}</p>
                    <p class="text-xs text-muted-foreground mt-2">talaba/para</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-success"></i>
                </div>
            </div>
        </div>

        {{-- Bugungi Yo'q --}}
        <div class="bg-card rounded-xl border border-border p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground">Bugun Kelmadi</p>
                    <p class="text-3xl font-bold text-destructive mt-1" id="bugunYoq">{{ $bugungiUmumiy['yoq'] }}</p>
                    <p class="text-xs text-muted-foreground mt-2">talaba/para</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-destructive/10 flex items-center justify-center">
                    <i data-lucide="user-x" class="w-6 h-6 text-destructive"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== BUGUNGI 4 PARA STATISTIKASI ===== --}}
    <div class="bg-card rounded-2xl border border-border overflow-hidden">
        <div class="p-6 border-b border-border">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-primary"></i>
                    Bugungi Paralar - Batafsil Hisobot
                </h2>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <span class="text-xs text-muted-foreground">Real-time</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-border">
            @foreach($bugungiParaStatistikasi as $para => $stat)
            <div class="p-5 {{ $stat['hozirgi'] ? 'bg-primary/5' : '' }}">
                {{-- Para Header --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-lg
                            {{ $stat['holat'] === 'davom' ? 'bg-primary text-white' : ($stat['holat'] === 'tugagan' ? 'bg-success/20 text-success' : 'bg-muted text-muted-foreground') }}">
                            {{ $para }}
                        </div>
                        <div>
                            <p class="font-medium text-foreground">{{ $para }}-para</p>
                            <p class="text-xs text-muted-foreground">{{ $stat['vaqt']['boshlanish'] }} - {{ $stat['vaqt']['tugash'] }}</p>
                        </div>
                    </div>
                    @if($stat['holat'] === 'davom')
                        <span class="px-2 py-1 text-xs font-medium bg-primary/20 text-primary rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                            Davom etmoqda
                        </span>
                    @elseif($stat['holat'] === 'tugagan')
                        <span class="px-2 py-1 text-xs font-medium bg-success/20 text-success rounded-full">
                            ✓ Tugagan
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-medium bg-muted text-muted-foreground rounded-full">
                            Kutilmoqda
                        </span>
                    @endif
                </div>

                @if($stat['davomat_olingan'] > 0 || $stat['holat'] !== 'kutilmoqda')
                {{-- Jami talabalar --}}
                <div class="mb-3 p-2 bg-muted/50 rounded-lg text-center">
                    <p class="text-xs text-muted-foreground">Jami talabalar</p>
                    <p class="text-lg font-bold text-foreground">{{ $stat['jami_talabalar'] }}</p>
                </div>

                {{-- Bor/Yo'q/Olinmagan - 3 ta ustun --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="bg-success/10 rounded-lg p-2 text-center border border-success/20">
                        <p class="text-lg font-bold text-success">{{ $stat['bor'] }}</p>
                        <p class="text-xs text-success/80">Keldi</p>
                        <p class="text-xs font-medium text-success">{{ $stat['bor_foiz'] }}%</p>
                    </div>
                    <div class="bg-destructive/10 rounded-lg p-2 text-center border border-destructive/20">
                        <p class="text-lg font-bold text-destructive">{{ $stat['yoq'] }}</p>
                        <p class="text-xs text-destructive/80">Kelmadi</p>
                        <p class="text-xs font-medium text-destructive">{{ $stat['yoq_foiz'] }}%</p>
                    </div>
                    <div class="bg-warning/10 rounded-lg p-2 text-center border border-warning/20">
                        <p class="text-lg font-bold text-warning">{{ $stat['davomat_olinmagan'] }}</p>
                        <p class="text-xs text-warning/80">Olinmagan</p>
                        <p class="text-xs font-medium text-warning">{{ $stat['olinmagan_foiz'] }}%</p>
                    </div>
                </div>

                {{-- Davomat olinganlar foizi --}}
                @if($stat['davomat_olingan'] > 0)
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-muted-foreground">Olinganlar ichida keldi</span>
                        <span class="text-sm font-bold {{ $stat['davomat_foiz'] >= 80 ? 'text-success' : ($stat['davomat_foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                            {{ $stat['davomat_foiz'] }}%
                        </span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500 {{ $stat['davomat_foiz'] >= 80 ? 'bg-success' : ($stat['davomat_foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                             style="width: {{ $stat['davomat_foiz'] }}%"></div>
                    </div>
                </div>
                @endif

                {{-- Kurslar bo'yicha --}}
                <div class="space-y-2 pt-3 border-t border-border">
                    <p class="text-xs text-muted-foreground font-medium">Kurslar bo'yicha:</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2 bg-primary/5 rounded border border-primary/20">
                            <p class="font-medium text-primary">1-kurs</p>
                            <p class="text-success">✓ {{ $stat['kurs1']['bor'] }} ({{ $stat['kurs1']['bor_foiz'] }}%)</p>
                            <p class="text-destructive">✗ {{ $stat['kurs1']['yoq'] }} ({{ $stat['kurs1']['yoq_foiz'] }}%)</p>
                            <p class="text-warning">○ {{ $stat['kurs1']['davomat_olinmagan'] }} ({{ $stat['kurs1']['olinmagan_foiz'] }}%)</p>
                        </div>
                        <div class="p-2 bg-secondary/5 rounded border border-secondary/20">
                            <p class="font-medium text-secondary">2-kurs</p>
                            <p class="text-success">✓ {{ $stat['kurs2']['bor'] }} ({{ $stat['kurs2']['bor_foiz'] }}%)</p>
                            <p class="text-destructive">✗ {{ $stat['kurs2']['yoq'] }} ({{ $stat['kurs2']['yoq_foiz'] }}%)</p>
                            <p class="text-warning">○ {{ $stat['kurs2']['davomat_olinmagan'] }} ({{ $stat['kurs2']['olinmagan_foiz'] }}%)</p>
                        </div>
                    </div>
                </div>

                {{-- Guruhlar holati --}}
                @if($stat['holat'] === 'tugagan' && $stat['olinmagan_guruhlar'] > 0)
                <div class="mt-3 p-2 bg-warning/10 rounded-lg border border-warning/20">
                    <p class="text-xs text-warning flex items-center gap-1">
                        <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                        {{ $stat['olinmagan_guruhlar'] }} guruhdan davomat olinmagan
                    </p>
                </div>
                @elseif($stat['holat'] === 'tugagan')
                <div class="mt-3 p-2 bg-success/10 rounded-lg border border-success/20">
                    <p class="text-xs text-success flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                        {{ $stat['davomat_olingan_guruhlar'] }}/{{ $stat['jami_guruhlar'] }} guruhdan olindi
                    </p>
                </div>
                @endif
                @else
                {{-- Para boshlanmagan --}}
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-muted/50 flex items-center justify-center mb-3">
                        <i data-lucide="clock" class="w-6 h-6 text-muted-foreground"></i>
                    </div>
                    <p class="text-sm text-muted-foreground">Para hali boshlanmadi</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ===== DAVR BO'YICHA STATISTIKA ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Bugungi Umumiy --}}
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="calendar-check" class="w-5 h-5 text-primary"></i>
                    Bugun
                </h3>
                <span class="px-2 py-1 text-xs font-medium bg-primary/10 text-primary rounded-full">
                    {{ now()->format('d.m.Y') }}
                </span>
            </div>

            {{-- Jami ma'lumot --}}
            <div class="text-center mb-4">
                <p class="text-xs text-muted-foreground">Jami: {{ $bugungiUmumiy['jami_talabalar'] }} talaba × 4 para</p>
                <p class="text-lg font-bold text-foreground">{{ $bugungiUmumiy['jami_kutilayotgan'] }} davomat kutilmoqda</p>
            </div>

            {{-- 3 ta ustun: Keldi, Kelmadi, Olinmagan --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="bg-success/10 rounded-lg p-3 text-center border border-success/20">
                    <p class="text-xl font-bold text-success">{{ $bugungiUmumiy['bor'] }}</p>
                    <p class="text-xs text-success/80">Keldi</p>
                    <p class="text-sm font-bold text-success">{{ $bugungiUmumiy['bor_foiz'] }}%</p>
                </div>
                <div class="bg-destructive/10 rounded-lg p-3 text-center border border-destructive/20">
                    <p class="text-xl font-bold text-destructive">{{ $bugungiUmumiy['yoq'] }}</p>
                    <p class="text-xs text-destructive/80">Kelmadi</p>
                    <p class="text-sm font-bold text-destructive">{{ $bugungiUmumiy['yoq_foiz'] }}%</p>
                </div>
                <div class="bg-warning/10 rounded-lg p-3 text-center border border-warning/20">
                    <p class="text-xl font-bold text-warning">{{ $bugungiUmumiy['davomat_olinmagan'] }}</p>
                    <p class="text-xs text-warning/80">Olinmagan</p>
                    <p class="text-sm font-bold text-warning">{{ $bugungiUmumiy['olinmagan_foiz'] }}%</p>
                </div>
            </div>

            {{-- Olinganlar ichida foiz --}}
            @if($bugungiUmumiy['davomat_olingan'] > 0)
            <div class="p-2 bg-muted/50 rounded-lg text-center">
                <p class="text-xs text-muted-foreground">Olinganlar ichida keldi</p>
                <p class="text-lg font-bold {{ $bugungiUmumiy['davomat_foiz'] >= 80 ? 'text-success' : ($bugungiUmumiy['davomat_foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}" id="bugunFoiz">
                    {{ $bugungiUmumiy['davomat_foiz'] }}%
                </p>
            </div>
            @endif
        </div>

        {{-- Haftalik --}}
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="calendar-range" class="w-5 h-5 text-secondary"></i>
                    Bu Hafta
                </h3>
                <span class="px-2 py-1 text-xs font-medium bg-secondary/10 text-secondary rounded-full">
                    {{ $haftalikStatistika['boshlanish'] }} - {{ $haftalikStatistika['tugash'] }} ({{ $haftalikStatistika['kunlar'] }} kun)
                </span>
            </div>

            {{-- 3 ta ustun: Keldi, Kelmadi, Olinmagan --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="bg-success/10 rounded-lg p-2 text-center border border-success/20">
                    <p class="text-lg font-bold text-success">{{ number_format($haftalikStatistika['bor']) }}</p>
                    <p class="text-xs text-success/80">Keldi</p>
                    <p class="text-xs font-bold text-success">{{ $haftalikStatistika['bor_foiz'] }}%</p>
                </div>
                <div class="bg-destructive/10 rounded-lg p-2 text-center border border-destructive/20">
                    <p class="text-lg font-bold text-destructive">{{ number_format($haftalikStatistika['yoq']) }}</p>
                    <p class="text-xs text-destructive/80">Kelmadi</p>
                    <p class="text-xs font-bold text-destructive">{{ $haftalikStatistika['yoq_foiz'] }}%</p>
                </div>
                <div class="bg-warning/10 rounded-lg p-2 text-center border border-warning/20">
                    <p class="text-lg font-bold text-warning">{{ number_format($haftalikStatistika['davomat_olinmagan']) }}</p>
                    <p class="text-xs text-warning/80">Olinmagan</p>
                    <p class="text-xs font-bold text-warning">{{ $haftalikStatistika['olinmagan_foiz'] }}%</p>
                </div>
            </div>

            {{-- Olinganlar ichida foiz --}}
            @if($haftalikStatistika['davomat_olingan'] > 0)
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-muted-foreground">Olinganlar ichida keldi</span>
                    <span class="text-sm font-bold {{ $haftalikStatistika['davomat_foiz'] >= 80 ? 'text-success' : ($haftalikStatistika['davomat_foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                        {{ $haftalikStatistika['davomat_foiz'] }}%
                    </span>
                </div>
                <div class="w-full bg-muted rounded-full h-2">
                    <div class="h-2 rounded-full {{ $haftalikStatistika['davomat_foiz'] >= 80 ? 'bg-success' : ($haftalikStatistika['davomat_foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                         style="width: {{ $haftalikStatistika['davomat_foiz'] }}%"></div>
                </div>
            </div>
            @endif

            {{-- Kurslar bo'yicha --}}
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2 bg-primary/5 rounded border border-primary/20">
                    <p class="font-medium text-primary mb-1">1-kurs</p>
                    <p class="text-success">✓ {{ $haftalikStatistika['kurs1']['bor'] }} ({{ $haftalikStatistika['kurs1']['bor_foiz'] }}%)</p>
                    <p class="text-destructive">✗ {{ $haftalikStatistika['kurs1']['yoq'] }} ({{ $haftalikStatistika['kurs1']['yoq_foiz'] }}%)</p>
                    <p class="text-warning">○ {{ $haftalikStatistika['kurs1']['davomat_olinmagan'] }} ({{ $haftalikStatistika['kurs1']['olinmagan_foiz'] }}%)</p>
                </div>
                <div class="p-2 bg-secondary/5 rounded border border-secondary/20">
                    <p class="font-medium text-secondary mb-1">2-kurs</p>
                    <p class="text-success">✓ {{ $haftalikStatistika['kurs2']['bor'] }} ({{ $haftalikStatistika['kurs2']['bor_foiz'] }}%)</p>
                    <p class="text-destructive">✗ {{ $haftalikStatistika['kurs2']['yoq'] }} ({{ $haftalikStatistika['kurs2']['yoq_foiz'] }}%)</p>
                    <p class="text-warning">○ {{ $haftalikStatistika['kurs2']['davomat_olinmagan'] }} ({{ $haftalikStatistika['kurs2']['olinmagan_foiz'] }}%)</p>
                </div>
            </div>
        </div>

        {{-- Oylik --}}
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-accent"></i>
                    Bu Oy
                </h3>
                <span class="px-2 py-1 text-xs font-medium bg-accent/10 text-accent rounded-full">
                    {{ $oylikStatistika['oy_nomi'] }} ({{ $oylikStatistika['kunlar'] }} kun)
                </span>
            </div>

            {{-- 3 ta ustun: Keldi, Kelmadi, Olinmagan --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <div class="bg-success/10 rounded-lg p-2 text-center border border-success/20">
                    <p class="text-lg font-bold text-success">{{ number_format($oylikStatistika['bor']) }}</p>
                    <p class="text-xs text-success/80">Keldi</p>
                    <p class="text-xs font-bold text-success">{{ $oylikStatistika['bor_foiz'] }}%</p>
                </div>
                <div class="bg-destructive/10 rounded-lg p-2 text-center border border-destructive/20">
                    <p class="text-lg font-bold text-destructive">{{ number_format($oylikStatistika['yoq']) }}</p>
                    <p class="text-xs text-destructive/80">Kelmadi</p>
                    <p class="text-xs font-bold text-destructive">{{ $oylikStatistika['yoq_foiz'] }}%</p>
                </div>
                <div class="bg-warning/10 rounded-lg p-2 text-center border border-warning/20">
                    <p class="text-lg font-bold text-warning">{{ number_format($oylikStatistika['davomat_olinmagan']) }}</p>
                    <p class="text-xs text-warning/80">Olinmagan</p>
                    <p class="text-xs font-bold text-warning">{{ $oylikStatistika['olinmagan_foiz'] }}%</p>
                </div>
            </div>

            {{-- Olinganlar ichida foiz --}}
            @if($oylikStatistika['davomat_olingan'] > 0)
            <div class="mb-3">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-muted-foreground">Olinganlar ichida keldi</span>
                    <span class="text-sm font-bold {{ $oylikStatistika['davomat_foiz'] >= 80 ? 'text-success' : ($oylikStatistika['davomat_foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                        {{ $oylikStatistika['davomat_foiz'] }}%
                    </span>
                </div>
                <div class="w-full bg-muted rounded-full h-2">
                    <div class="h-2 rounded-full {{ $oylikStatistika['davomat_foiz'] >= 80 ? 'bg-success' : ($oylikStatistika['davomat_foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                         style="width: {{ $oylikStatistika['davomat_foiz'] }}%"></div>
                </div>
            </div>
            @endif

            {{-- Kurslar bo'yicha --}}
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2 bg-primary/5 rounded border border-primary/20">
                    <p class="font-medium text-primary mb-1">1-kurs</p>
                    <p class="text-success">✓ {{ $oylikStatistika['kurs1']['bor'] }} ({{ $oylikStatistika['kurs1']['bor_foiz'] }}%)</p>
                    <p class="text-destructive">✗ {{ $oylikStatistika['kurs1']['yoq'] }} ({{ $oylikStatistika['kurs1']['yoq_foiz'] }}%)</p>
                    <p class="text-warning">○ {{ $oylikStatistika['kurs1']['davomat_olinmagan'] }} ({{ $oylikStatistika['kurs1']['olinmagan_foiz'] }}%)</p>
                </div>
                <div class="p-2 bg-secondary/5 rounded border border-secondary/20">
                    <p class="font-medium text-secondary mb-1">2-kurs</p>
                    <p class="text-success">✓ {{ $oylikStatistika['kurs2']['bor'] }} ({{ $oylikStatistika['kurs2']['bor_foiz'] }}%)</p>
                    <p class="text-destructive">✗ {{ $oylikStatistika['kurs2']['yoq'] }} ({{ $oylikStatistika['kurs2']['yoq_foiz'] }}%)</p>
                    <p class="text-warning">○ {{ $oylikStatistika['kurs2']['davomat_olinmagan'] }} ({{ $oylikStatistika['kurs2']['olinmagan_foiz'] }}%)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== KURSLAR BO'YICHA STATISTIKA ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($kurslarStatistikasi as $kurs => $stat)
        <div class="bg-gradient-to-br from-{{ $kurs == 1 ? 'primary' : 'secondary' }}/5 to-transparent rounded-xl border border-{{ $kurs == 1 ? 'primary' : 'secondary' }}/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-{{ $kurs == 1 ? 'primary' : 'secondary' }}/20 flex items-center justify-center">
                        <span class="text-2xl font-bold text-{{ $kurs == 1 ? 'primary' : 'secondary' }}">{{ $kurs }}</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-foreground">{{ $kurs }}-Kurs</h3>
                        <p class="text-sm text-muted-foreground">{{ $stat['jami_guruhlar'] }} guruh, {{ $stat['jami_talabalar'] }} talaba</p>
                        <p class="text-xs text-muted-foreground">Jami kutilmoqda: {{ $stat['jami_kutilayotgan'] }}</p>
                    </div>
                </div>
            </div>

            {{-- 3 ta ustun: Keldi, Kelmadi, Olinmagan --}}
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-success/10 rounded-lg p-3 text-center border border-success/20">
                    <p class="text-2xl font-bold text-success">{{ $stat['bor'] }}</p>
                    <p class="text-xs text-success/80">Keldi</p>
                    <p class="text-sm font-bold text-success">{{ $stat['bor_foiz'] }}%</p>
                </div>
                <div class="bg-destructive/10 rounded-lg p-3 text-center border border-destructive/20">
                    <p class="text-2xl font-bold text-destructive">{{ $stat['yoq'] }}</p>
                    <p class="text-xs text-destructive/80">Kelmadi</p>
                    <p class="text-sm font-bold text-destructive">{{ $stat['yoq_foiz'] }}%</p>
                </div>
                <div class="bg-warning/10 rounded-lg p-3 text-center border border-warning/20">
                    <p class="text-2xl font-bold text-warning">{{ $stat['davomat_olinmagan'] }}</p>
                    <p class="text-xs text-warning/80">Olinmagan</p>
                    <p class="text-sm font-bold text-warning">{{ $stat['olinmagan_foiz'] }}%</p>
                </div>
            </div>

            {{-- Olinganlar ichida keldi foizi --}}
            @if($stat['davomat_olingan'] > 0)
            <div class="p-3 bg-muted/50 rounded-lg text-center">
                <p class="text-xs text-muted-foreground">Olinganlar ichida keldi</p>
                <p class="text-2xl font-bold {{ $stat['davomat_foiz'] >= 80 ? 'text-success' : ($stat['davomat_foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                    {{ $stat['davomat_foiz'] }}%
                </p>
                <div class="w-full bg-muted rounded-full h-2 mt-2">
                    <div class="h-2 rounded-full {{ $stat['davomat_foiz'] >= 80 ? 'bg-success' : ($stat['davomat_foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                         style="width: {{ $stat['davomat_foiz'] }}%"></div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ===== 7 KUNLIK TREND GRAFIK ===== --}}
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <i data-lucide="trending-up" class="w-5 h-5 text-primary"></i>
                Oxirgi 7 Kunlik Trend
            </h3>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-primary"></div>
                    <span class="text-muted-foreground">Davomat %</span>
                </div>
            </div>
        </div>
        <div style="height: 300px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ===== GURUHLAR VA TOP YO'QLIKLAR ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Guruhlar Statistikasi --}}
        <div class="lg:col-span-2 bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="layout-grid" class="w-5 h-5 text-secondary"></i>
                    Guruhlar Bo'yicha
                </h3>
                <div class="flex items-center gap-2">
                    <input type="text" id="guruhSearch" placeholder="Qidirish..."
                           class="w-40 px-3 py-1.5 text-sm bg-input border border-border rounded-lg focus:ring-2 focus:ring-ring">
                    <select id="guruhFilter" class="px-3 py-1.5 text-sm bg-input border border-border rounded-lg focus:ring-2 focus:ring-ring">
                        <option value="all">Barchasi</option>
                        <option value="1">1-kurs</option>
                        <option value="2">2-kurs</option>
                    </select>
                </div>
            </div>

            @if(count($guruhlarStatistikasi) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 max-h-96 overflow-y-auto pr-2" id="guruhlarGrid">
                @foreach($guruhlarStatistikasi as $stat)
                <div class="guruh-card p-4 bg-muted/30 rounded-lg border border-border hover:bg-muted/50 transition-colors cursor-pointer"
                     data-guruh="{{ strtolower($stat['guruh']->nomi) }}"
                     data-kurs="{{ $stat['guruh']->kurs }}">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="font-medium text-foreground text-sm">{{ $stat['guruh']->nomi }}</p>
                            <p class="text-xs text-muted-foreground">{{ $stat['guruh']->kurs }}-kurs • {{ $stat['talabalar_soni'] }} talaba</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-bold rounded-full
                            {{ $stat['foiz'] >= 80 ? 'bg-success/20 text-success' : ($stat['foiz'] >= 60 ? 'bg-warning/20 text-warning' : 'bg-destructive/20 text-destructive') }}">
                            {{ $stat['foiz'] }}%
                        </span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $stat['foiz'] >= 80 ? 'bg-success' : ($stat['foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                             style="width: {{ $stat['foiz'] }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs mt-2">
                        <span class="text-success">✓ {{ $stat['bor'] }}</span>
                        <span class="text-destructive">✗ {{ $stat['yoq'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="noGuruhResults" class="hidden text-center py-8 text-muted-foreground">
                <i data-lucide="search-x" class="w-10 h-10 mx-auto mb-2 opacity-50"></i>
                <p>Guruh topilmadi</p>
            </div>
            @else
            <div class="text-center py-12 text-muted-foreground">
                <i data-lucide="clipboard" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                <p>Bugun hali davomat olinmagan</p>
            </div>
            @endif
        </div>

        {{-- Top Yo'qliklar --}}
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-destructive"></i>
                    Top Yo'qliklar
                </h3>
                <span class="px-2 py-1 text-xs font-medium bg-destructive/10 text-destructive rounded-full">Bu oy</span>
            </div>

            @if(count($topYoqlar) > 0)
            <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                @foreach($topYoqlar as $index => $item)
                <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg border border-border hover:bg-muted/50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold
                            {{ $index < 3 ? 'bg-destructive/20 text-destructive' : 'bg-muted text-muted-foreground' }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-foreground truncate">{{ $item['talaba']->fish }}</p>
                            <p class="text-xs text-muted-foreground">{{ $item['talaba']->guruh?->nomi ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-bold bg-destructive/20 text-destructive rounded-full">
                        {{ $item['yoq_soni'] }} para
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-12 h-12 rounded-full bg-success/20 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="check-circle" class="w-6 h-6 text-success"></i>
                </div>
                <p class="text-muted-foreground text-sm">Yo'qliklar yo'q!</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== TEZKOR HARAKATLAR ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @if(auth()->user()->canTakeAttendance())
        <a href="{{ route('davomat.olish') }}" class="group bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl border border-primary/20 p-5 hover:from-primary/20 hover:to-primary/10 transition-all hover:shadow-lg">
            <div class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center mb-3 group-hover:bg-primary/30 transition-colors">
                <i data-lucide="clipboard-check" class="w-5 h-5 text-primary"></i>
            </div>
            <h4 class="font-semibold text-foreground">Davomat Olish</h4>
            <p class="text-xs text-muted-foreground mt-1">Bugungi davomatni belgilash</p>
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <a href="{{ route('guruhlar.index') }}" class="group bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-xl border border-secondary/20 p-5 hover:from-secondary/20 hover:to-secondary/10 transition-all hover:shadow-lg">
            <div class="w-10 h-10 rounded-lg bg-secondary/20 flex items-center justify-center mb-3 group-hover:bg-secondary/30 transition-colors">
                <i data-lucide="users" class="w-5 h-5 text-secondary"></i>
            </div>
            <h4 class="font-semibold text-foreground">Guruhlar</h4>
            <p class="text-xs text-muted-foreground mt-1">Barcha guruhlarni boshqarish</p>
        </a>

        <a href="{{ route('talabalar.index') }}" class="group bg-gradient-to-br from-success/10 to-success/5 rounded-xl border border-success/20 p-5 hover:from-success/20 hover:to-success/10 transition-all hover:shadow-lg">
            <div class="w-10 h-10 rounded-lg bg-success/20 flex items-center justify-center mb-3 group-hover:bg-success/30 transition-colors">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-success"></i>
            </div>
            <h4 class="font-semibold text-foreground">Talabalar</h4>
            <p class="text-xs text-muted-foreground mt-1">Talabalar ma'lumotlari</p>
        </a>

        <a href="{{ route('export.index') }}" class="group bg-gradient-to-br from-accent/10 to-accent/5 rounded-xl border border-accent/20 p-5 hover:from-accent/20 hover:to-accent/10 transition-all hover:shadow-lg">
            <div class="w-10 h-10 rounded-lg bg-accent/20 flex items-center justify-center mb-3 group-hover:bg-accent/30 transition-colors">
                <i data-lucide="download" class="w-5 h-5 text-accent"></i>
            </div>
            <h4 class="font-semibold text-foreground">Export</h4>
            <p class="text-xs text-muted-foreground mt-1">Excel hisobotlar</p>
        </a>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function dashboardApp() {
    return {
        init() {
            this.startRealTimeUpdates();
            this.initGuruhFilter();
        },

        startRealTimeUpdates() {
            // Har 30 sekundda yangilash
            setInterval(() => this.fetchStats(), 30000);
            // Har sekundda vaqtni yangilash
            setInterval(() => this.updateTime(), 1000);
        },

        async fetchStats() {
            try {
                const res = await fetch('{{ route("dashboard.realtime") }}');
                const data = await res.json();

                if (data.bugungi_umumiy) {
                    document.getElementById('bugunBor').textContent = data.bugungi_umumiy.bor;
                    document.getElementById('bugunYoq').textContent = data.bugungi_umumiy.yoq;
                    document.getElementById('bugunFoiz').textContent = data.bugungi_umumiy.foiz;
                }

                if (data.server_vaqt) {
                    document.getElementById('serverTime').textContent = data.server_vaqt;
                }
            } catch (e) {
                console.error('Stats fetch error:', e);
            }
        },

        updateTime() {
            fetch('{{ route("dashboard.refresh") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.para_holati?.hozirgi_vaqt) {
                        document.getElementById('serverTime').textContent = data.para_holati.hozirgi_vaqt;
                    }
                })
                .catch(() => {});
        },

        initGuruhFilter() {
            const search = document.getElementById('guruhSearch');
            const filter = document.getElementById('guruhFilter');
            const cards = document.querySelectorAll('.guruh-card');
            const noResults = document.getElementById('noGuruhResults');

            const filterCards = () => {
                const term = search?.value?.toLowerCase() || '';
                const kurs = filter?.value || 'all';
                let visible = 0;

                cards.forEach(card => {
                    const name = card.dataset.guruh;
                    const cardKurs = card.dataset.kurs;
                    const matchSearch = name.includes(term);
                    const matchFilter = kurs === 'all' || cardKurs === kurs;

                    if (matchSearch && matchFilter) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', visible > 0);
                }
            };

            search?.addEventListener('input', filterCards);
            filter?.addEventListener('change', filterCards);
        }
    }
}

// Trend Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const trendData = @json($kunlikTrend);

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(8, 145, 178, 0.3)');
    gradient.addColorStop(1, 'rgba(8, 145, 178, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(d => d.kun + ' ' + d.sana),
            datasets: [{
                label: 'Davomat %',
                data: trendData.map(d => d.foiz),
                borderColor: '#0891b2',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0891b2',
                pointBorderColor: 'var(--card)',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'var(--card)',
                    titleColor: 'var(--foreground)',
                    bodyColor: 'var(--muted-foreground)',
                    borderColor: 'rgba(8, 145, 178, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: ctx => 'Davomat: ' + ctx.parsed.y + '%'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: v => v + '%',
                        color: 'var(--muted-foreground)'
                    },
                    grid: { color: 'rgba(8, 145, 178, 0.1)' }
                },
                x: {
                    ticks: { color: 'var(--muted-foreground)' },
                    grid: { display: false }
                }
            }
        }
    });

    // Icons
    setTimeout(() => lucide.createIcons(), 100);
});
</script>
@endpush
