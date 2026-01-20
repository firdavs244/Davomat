@extends('layouts.app')

@section('title', 'Dashboard - Kollej Davomat Tizimi')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="dashboardData()">

    <!-- Xush kelibsiz Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-primary/20 via-secondary/20 to-accent/20 rounded-xl border border-border p-6">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-foreground mb-2 flex items-center gap-2">
                    <i data-lucide="hand-wave" class="w-7 h-7 text-accent"></i>
                    Xush kelibsiz, {{ auth()->user()->name }}!
                </h1>
                <p class="text-muted-foreground">
                    Bugungi kun: {{ now()->locale('uz')->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
            <!-- Hozirgi vaqt va para -->
            <div class="flex items-center gap-4">
                <div class="text-center">
                    <p class="text-xs text-muted-foreground">Hozirgi vaqt</p>
                    <p class="text-xl font-bold text-primary" id="currentTime">{{ $paraHolati['hozirgi_vaqt'] ?? '--:--:--' }}</p>
                </div>
                @if($paraHolati['davomat_para'] ?? null)
                <div class="h-10 w-px bg-border"></div>
                <div class="text-center">
                    <p class="text-xs text-muted-foreground">Davomat para</p>
                    <p class="text-xl font-bold text-success">{{ $paraHolati['davomat_para'] }}-para</p>
                </div>
                @endif
            </div>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-primary/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Real-time Kurs bo'yicha Statistika (faqat admin uchun) -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-primary"></i>
                Hozirgi Para Statistikasi (Real-time)
            </h3>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs font-medium bg-primary/20 text-primary rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                    Jonli
                </span>
                <span class="text-sm text-muted-foreground" id="lastUpdated">Yangilandi: --:--:--</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="kursStats">
            @foreach([1, 2] as $kurs)
            <div class="bg-gradient-to-br from-{{ $kurs == 1 ? 'primary' : 'secondary' }}/10 to-transparent rounded-xl border border-{{ $kurs == 1 ? 'primary' : 'secondary' }}/30 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-{{ $kurs == 1 ? 'primary' : 'secondary' }}/20 flex items-center justify-center">
                            <span class="text-2xl font-bold text-{{ $kurs == 1 ? 'primary' : 'secondary' }}">{{ $kurs }}</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-foreground">{{ $kurs }}-kurs</h4>
                            <p class="text-sm text-muted-foreground" id="kurs{{ $kurs }}Info">
                                {{ $kursStatistikasi[$kurs]['jami_talabalar'] ?? 0 }} talaba, {{ $kursStatistikasi[$kurs]['guruhlar_soni'] ?? 0 }} guruh
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold {{ ($kursStatistikasi[$kurs]['foiz'] ?? 0) >= 80 ? 'text-success' : (($kursStatistikasi[$kurs]['foiz'] ?? 0) >= 60 ? 'text-warning' : 'text-destructive') }}"
                           id="kurs{{ $kurs }}Foiz">
                            {{ $kursStatistikasi[$kurs]['foiz'] ?? 0 }}%
                        </p>
                        <p class="text-xs text-muted-foreground">Davomat foizi</p>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="mb-4">
                    <div class="progress-bar h-3">
                        <div class="progress-fill h-full transition-all duration-500 {{ ($kursStatistikasi[$kurs]['foiz'] ?? 0) >= 80 ? 'bg-success' : (($kursStatistikasi[$kurs]['foiz'] ?? 0) >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                             id="kurs{{ $kurs }}Progress"
                             style="width: {{ $kursStatistikasi[$kurs]['foiz'] ?? 0 }}%"></div>
                    </div>
                </div>

                <!-- Bor/Yo'q statistikasi -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-success/10 rounded-lg p-3 text-center border border-success/20">
                        <p class="text-2xl font-bold text-success" id="kurs{{ $kurs }}Bor">{{ $kursStatistikasi[$kurs]['bor'] ?? 0 }}</p>
                        <p class="text-xs text-success/80 flex items-center justify-center gap-1">
                            <i data-lucide="check" class="w-3 h-3"></i> Bor
                        </p>
                    </div>
                    <div class="bg-destructive/10 rounded-lg p-3 text-center border border-destructive/20">
                        <p class="text-2xl font-bold text-destructive" id="kurs{{ $kurs }}Yoq">{{ $kursStatistikasi[$kurs]['yoq'] ?? 0 }}</p>
                        <p class="text-xs text-destructive/80 flex items-center justify-center gap-1">
                            <i data-lucide="x" class="w-3 h-3"></i> Yo'q
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Para vaqtlari jadvali -->
        <div class="mt-6 pt-6 border-t border-border">
            <h4 class="text-sm font-medium text-muted-foreground mb-3">Para vaqtlari</h4>
            <div class="grid grid-cols-3 gap-4">
                @foreach([1 => ['08:00', '09:20'], 2 => ['09:30', '10:50'], 3 => ['11:00', '12:20']] as $para => $vaqtlar)
                <div class="text-center p-3 rounded-lg border {{ ($paraHolati['davomat_para'] ?? 0) == $para ? 'bg-primary/10 border-primary/30' : 'bg-muted/30 border-border' }}">
                    <span class="text-sm font-medium {{ ($paraHolati['davomat_para'] ?? 0) == $para ? 'text-primary' : 'text-foreground' }}">{{ $para }}-para</span>
                    <p class="text-xs text-muted-foreground mt-1">{{ $vaqtlar[0] }} - {{ $vaqtlar[1] }}</p>
                    @if(($paraHolati['mavjud_paralar'] ?? []) && in_array($para, $paraHolati['mavjud_paralar']))
                    <span class="inline-flex items-center gap-1 text-xs text-success mt-1">
                        <i data-lucide="check-circle" class="w-3 h-3"></i> Tugagan
                    </span>
                    @elseif(($paraHolati['hozirgi_para'] ?? 0) == $para)
                    <span class="inline-flex items-center gap-1 text-xs text-primary mt-1">
                        <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span> Davom etmoqda
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tugagan Paralar Umumiy Ma'lumoti -->
    @if(isset($tugaganParalarSummary))
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <i data-lucide="clipboard-check" class="w-5 h-5 text-success"></i>
                Tugagan Paralar - Umumiy Ma'lumot
            </h3>
            <a href="{{ route('davomat.hisobot') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Batafsil hisobot
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach([1, 2, 3] as $para)
                @php $paraSummary = $tugaganParalarSummary[$para] ?? null; @endphp
                <div class="rounded-xl border {{ $paraSummary && $paraSummary['tugadi'] ? 'border-success/30 bg-success/5' : 'border-border bg-muted/30' }} p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-foreground">{{ $para }}-Para</h4>
                        @if($paraSummary && $paraSummary['tugadi'])
                            <span class="px-2 py-1 text-xs font-medium bg-success/20 text-success rounded-full">Tugagan</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium bg-muted text-muted-foreground rounded-full">Kutilmoqda</span>
                        @endif
                    </div>

                    @if($paraSummary && $paraSummary['tugadi'])
                        {{-- Guruhlar holati --}}
                        <div class="flex items-center justify-between text-sm mb-3">
                            <span class="text-muted-foreground">Davomat olindi:</span>
                            <span class="font-semibold text-foreground">
                                {{ $paraSummary['davomat_olingan_guruhlar'] }}/{{ $paraSummary['jami_guruhlar'] }} guruh
                            </span>
                        </div>

                        {{-- Kelganlar/Kelmaganlar --}}
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div class="bg-success/10 rounded-lg p-2 text-center">
                                <div class="text-lg font-bold text-success">{{ $paraSummary['keldi'] }}</div>
                                <div class="text-xs text-success/80">Keldi</div>
                            </div>
                            <div class="bg-destructive/10 rounded-lg p-2 text-center">
                                <div class="text-lg font-bold text-destructive">{{ $paraSummary['kelmadi'] }}</div>
                                <div class="text-xs text-destructive/80">Kelmadi</div>
                            </div>
                        </div>

                        {{-- Umumiy foiz --}}
                        <div class="mb-3">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-muted-foreground">Umumiy:</span>
                                <span class="font-bold {{ $paraSummary['foiz'] >= 80 ? 'text-success' : ($paraSummary['foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                                    {{ $paraSummary['foiz'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-muted rounded-full h-2">
                                <div class="h-2 rounded-full transition-all {{ $paraSummary['foiz'] >= 80 ? 'bg-success' : ($paraSummary['foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                                    style="width: {{ $paraSummary['foiz'] }}%"></div>
                            </div>
                        </div>

                        {{-- Kurslar bo'yicha --}}
                        <div class="space-y-2 pt-3 border-t border-border/50">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">1-kurs:</span>
                                <span class="font-medium {{ $paraSummary['kurs1']['foiz'] >= 80 ? 'text-success' : ($paraSummary['kurs1']['foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                                    {{ $paraSummary['kurs1']['keldi'] }}/{{ $paraSummary['kurs1']['jami'] }} ({{ $paraSummary['kurs1']['foiz'] }}%)
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">2-kurs:</span>
                                <span class="font-medium {{ $paraSummary['kurs2']['foiz'] >= 80 ? 'text-success' : ($paraSummary['kurs2']['foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}">
                                    {{ $paraSummary['kurs2']['keldi'] }}/{{ $paraSummary['kurs2']['jami'] }} ({{ $paraSummary['kurs2']['foiz'] }}%)
                                </span>
                            </div>
                        </div>

                        @if($paraSummary['olinmagan_guruhlar'] > 0)
                        <div class="mt-3 p-2 bg-warning/10 rounded-lg border border-warning/20">
                            <p class="text-xs text-warning flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                {{ $paraSummary['olinmagan_guruhlar'] }} guruhdan davomat olinmadi
                            </p>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-6 text-muted-foreground">
                            <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">Para hali tugamadi</p>
                            @if($paraSummary)
                            <p class="text-xs mt-1">{{ $paraSummary['vaqt']['boshlanish'] ?? '' }} - {{ $paraSummary['vaqt']['tugash'] ?? '' }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    <!-- Asosiy Statistika Kartochkalari -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Jami Guruhlar -->
        <div class="stat-card bg-card rounded-xl border border-border p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Jami Guruhlar</p>
                    <p class="text-3xl font-bold text-foreground">{{ $jamiGuruhlar }}</p>
                    <p class="text-xs text-primary mt-2 flex items-center gap-1">
                        <i data-lucide="trending-up" class="w-3 h-3"></i>
                        1 va 2-kurs
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-primary/20 flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-primary"></i>
                </div>
            </div>
        </div>

        <!-- Jami Talabalar -->
        <div class="stat-card bg-card rounded-xl border border-border p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Aktiv Talabalar</p>
                    <p class="text-3xl font-bold text-foreground">{{ $jamiTalabalar }}</p>
                    <p class="text-xs text-success mt-2 flex items-center gap-1">
                        <i data-lucide="user-check" class="w-3 h-3"></i>
                        O'qiyotganlar
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-success/20 flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-7 h-7 text-success"></i>
                </div>
            </div>
        </div>

        <!-- Bugungi Davomat -->
        <div class="stat-card bg-card rounded-xl border border-border p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Bugungi Davomat</p>
                    <p class="text-3xl font-bold text-foreground" id="stat-davomat">{{ $bugungiStatistika['davomat_olingan'] }}</p>
                    <p class="text-xs text-secondary mt-2 flex items-center gap-1">
                        <i data-lucide="clipboard-check" class="w-3 h-3"></i>
                        Talaba davomati
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-secondary/20 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="w-7 h-7 text-secondary"></i>
                </div>
            </div>
        </div>

        <!-- Bugungi Foiz -->
        <div class="stat-card bg-card rounded-xl border border-border p-6 hover-lift">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Bugungi Davomat</p>
                    <p class="text-3xl font-bold {{ $bugungiStatistika['foiz'] >= 80 ? 'text-success' : ($bugungiStatistika['foiz'] >= 60 ? 'text-warning' : 'text-destructive') }}" id="stat-foiz">
                        {{ $bugungiStatistika['foiz'] }}%
                    </p>
                    <p class="text-xs text-accent mt-2 flex items-center gap-1">
                        <i data-lucide="percent" class="w-3 h-3"></i>
                        Umumiy foiz
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-accent/20 flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-7 h-7 text-accent"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Bugungi va Haftalik Statistika -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bugungi Statistika -->
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-primary"></i>
                    Bugungi Davomat
                </h3>
                <span class="px-3 py-1 text-xs font-medium bg-primary/20 text-primary rounded-full flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-primary rounded-full animate-pulse"></span>
                    Jonli
                </span>
            </div>

            <!-- Circular Progress -->
            <div class="flex justify-center mb-6">
                <div class="relative w-40 h-40">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="var(--muted)" stroke-width="8"/>
                        <circle cx="50" cy="50" r="45" fill="none"
                                stroke="{{ $bugungiStatistika['foiz'] >= 80 ? 'var(--success)' : ($bugungiStatistika['foiz'] >= 60 ? 'var(--warning)' : 'var(--destructive)') }}"
                                stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $bugungiStatistika['foiz'] * 2.83 }} 283"
                                class="transition-all duration-1000"
                                id="circleProgress"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-bold text-foreground" id="circlePercent">{{ $bugungiStatistika['foiz'] }}</span>
                        <span class="text-sm text-muted-foreground">foiz</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-success/10 rounded-lg p-4 text-center border border-success/20">
                    <p class="text-3xl font-bold text-success" id="stat-bor">{{ $bugungiStatistika['bor'] }}</p>
                    <p class="text-sm text-success/80 flex items-center justify-center gap-1">
                        <i data-lucide="check" class="w-4 h-4"></i> Bor
                    </p>
                </div>
                <div class="bg-destructive/10 rounded-lg p-4 text-center border border-destructive/20">
                    <p class="text-3xl font-bold text-destructive" id="stat-yoq">{{ $bugungiStatistika['yoq'] }}</p>
                    <p class="text-sm text-destructive/80 flex items-center justify-center gap-1">
                        <i data-lucide="x" class="w-4 h-4"></i> Yo'q
                    </p>
                </div>
            </div>
        </div>

        <!-- Haftalik O'rtacha -->
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-secondary"></i>
                    Haftalik O'rtacha
                </h3>
                <span class="px-3 py-1 text-xs font-medium bg-secondary/20 text-secondary rounded-full">7 kun</span>
            </div>

            <div class="flex items-center justify-center h-40">
                <div class="text-center">
                    <p class="text-6xl font-bold {{ $haftalikOrtacha >= 80 ? 'text-success' : ($haftalikOrtacha >= 60 ? 'text-warning' : 'text-destructive') }}">
                        {{ $haftalikOrtacha }}%
                    </p>
                    <p class="text-muted-foreground mt-2">Bu hafta o'rtacha</p>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex justify-between text-sm text-muted-foreground mb-2">
                    <span>Progress</span>
                    <span>{{ $haftalikOrtacha }}%</span>
                </div>
                <div class="progress-bar h-3">
                    <div class="progress-fill h-full bg-gradient-to-r {{ $haftalikOrtacha >= 80 ? 'from-success to-success/70' : ($haftalikOrtacha >= 60 ? 'from-warning to-warning/70' : 'from-destructive to-destructive/70') }}"
                         style="width: {{ $haftalikOrtacha }}%"></div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-border">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground flex items-center gap-1">
                        <i data-lucide="target" class="w-4 h-4"></i> Maqsad:
                    </span>
                    <span class="font-medium text-foreground">85%</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-muted-foreground flex items-center gap-1">
                        <i data-lucide="arrow-right-left" class="w-4 h-4"></i> Farq:
                    </span>
                    <span class="font-medium {{ $haftalikOrtacha >= 85 ? 'text-success' : 'text-warning' }}">
                        {{ $haftalikOrtacha >= 85 ? '+' : '' }}{{ $haftalikOrtacha - 85 }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Eng Ko'p Yo'q Bo'lgan Talabalar -->
        <div class="bg-card rounded-xl border border-border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-destructive"></i>
                    Yo'qliklar
                </h3>
                <span class="px-3 py-1 text-xs font-medium bg-destructive/20 text-destructive rounded-full">Bu oy</span>
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto pr-2" id="topYoqlarList">
                @if(count($engKopYoqTalabalar) > 0)
                    @foreach($engKopYoqTalabalar as $index => $item)
                    <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg border border-border hover:bg-muted/50 transition-colors">
                        <div class="flex items-center min-w-0">
                            <span class="w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold
                                {{ $index < 3 ? 'bg-destructive/20 text-destructive' : 'bg-muted text-muted-foreground' }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="ml-3 min-w-0">
                                <p class="text-sm font-medium text-foreground truncate">{{ $item['talaba']->fish }}</p>
                                <p class="text-xs text-muted-foreground">{{ $item['talaba']->guruh?->nomi ?? 'Guruhsiz' }}</p>
                            </div>
                        </div>
                        <span class="ml-2 px-2 py-1 text-xs font-bold rounded-full bg-destructive/20 text-destructive">
                            {{ $item['yoq_soni'] }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center justify-center h-64 text-center">
                        <div class="w-16 h-16 rounded-full bg-success/20 flex items-center justify-center mb-4">
                            <i data-lucide="check-circle-2" class="w-8 h-8 text-success"></i>
                        </div>
                        <p class="text-muted-foreground">Hozircha yo'qliklar yo'q!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Kunlik Trend Chart -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <i data-lucide="line-chart" class="w-5 h-5 text-primary"></i>
                Oxirgi 7 Kunlik Trend
            </h3>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-primary mr-2"></div>
                    <span class="text-sm text-muted-foreground">Davomat %</span>
                </div>
            </div>
        </div>
        <div style="height: 320px; position: relative;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Guruhlar Statistikasi -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <i data-lucide="layout-grid" class="w-5 h-5 text-secondary"></i>
                Guruhlar Bo'yicha Statistika
            </h3>

            <!-- Search va Filter -->
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input type="text"
                           id="guruhSearch"
                           placeholder="Guruh izlash..."
                           class="w-48 pl-10 pr-4 py-2 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-ring focus:border-transparent text-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <select id="guruhFilter" class="px-3 py-2 bg-input border border-border rounded-lg text-foreground text-sm focus:ring-2 focus:ring-ring">
                    <option value="all">Barchasi</option>
                    <option value="kurs1">1-kurs</option>
                    <option value="kurs2">2-kurs</option>
                    <option value="high">80%+ (Yaxshi)</option>
                    <option value="medium">60-80% (O'rta)</option>
                    <option value="low">60%- (Past)</option>
                </select>
            </div>
        </div>

        @if(count($guruhlarStatistikasi) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="guruhlarGrid">
            @foreach($guruhlarStatistikasi as $stat)
            <div class="guruh-card border border-border rounded-xl p-4 bg-muted/20 hover:bg-muted/40 transition-all hover-lift cursor-pointer"
                 data-guruh="{{ strtolower($stat['guruh']->nomi) }}"
                 data-kurs="{{ $stat['guruh']->kurs }}"
                 data-foiz="{{ $stat['foiz'] }}">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-heading font-semibold text-foreground">{{ $stat['guruh']->nomi }}</h4>
                        <p class="text-xs text-muted-foreground">{{ $stat['guruh']->kurs }}-kurs • {{ $stat['talabalar_soni'] }} talaba</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-bold rounded-full
                        {{ $stat['foiz'] >= 80 ? 'bg-success/20 text-success' : ($stat['foiz'] >= 60 ? 'bg-warning/20 text-warning' : 'bg-destructive/20 text-destructive') }}">
                        {{ $stat['foiz'] }}%
                    </span>
                </div>

                <div class="progress-bar h-2 mb-3">
                    <div class="progress-fill h-full {{ $stat['foiz'] >= 80 ? 'bg-success' : ($stat['foiz'] >= 60 ? 'bg-warning' : 'bg-destructive') }}"
                         style="width: {{ $stat['foiz'] }}%"></div>
                </div>

                <div class="flex justify-between text-xs">
                    <span class="text-success flex items-center gap-1"><i data-lucide="check" class="w-3 h-3"></i> {{ $stat['bor'] }} bor</span>
                    <span class="text-destructive flex items-center gap-1"><i data-lucide="x" class="w-3 h-3"></i> {{ $stat['yoq'] }} yo'q</span>
                </div>
            </div>
            @endforeach
        </div>

        <div id="noResults" class="hidden text-center py-12">
            <i data-lucide="search-x" class="w-16 h-16 text-muted-foreground mx-auto mb-4"></i>
            <p class="text-muted-foreground">Guruh topilmadi</p>
        </div>
        @else
        <div class="text-center py-12">
            <i data-lucide="clipboard" class="w-16 h-16 text-muted-foreground mx-auto mb-4"></i>
            <p class="text-muted-foreground">Bugun davomat olinmagan</p>
        </div>
        @endif
    </div>

    <!-- Tezkor Harakatlar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @if(auth()->user()->canTakeAttendance())
        <a href="{{ route('davomat.olish') }}" class="group bg-gradient-to-br from-primary/20 to-primary/5 rounded-xl border border-primary/30 p-6 hover:from-primary/30 hover:to-primary/10 transition-all hover-lift">
            <div class="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center mb-4 group-hover:bg-primary/30 transition-colors">
                <i data-lucide="clipboard-check" class="w-6 h-6 text-primary"></i>
            </div>
            <h4 class="font-semibold text-foreground mb-1">Davomat Olish</h4>
            <p class="text-sm text-muted-foreground">Bugungi davomatni belgilash</p>
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <a href="{{ route('guruhlar.index') }}" class="group bg-gradient-to-br from-secondary/20 to-secondary/5 rounded-xl border border-secondary/30 p-6 hover:from-secondary/30 hover:to-secondary/10 transition-all hover-lift">
            <div class="w-12 h-12 rounded-xl bg-secondary/20 flex items-center justify-center mb-4 group-hover:bg-secondary/30 transition-colors">
                <i data-lucide="users" class="w-6 h-6 text-secondary"></i>
            </div>
            <h4 class="font-semibold text-foreground mb-1">Guruhlar</h4>
            <p class="text-sm text-muted-foreground">Barcha guruhlarni boshqarish</p>
        </a>

        <a href="{{ route('talabalar.index') }}" class="group bg-gradient-to-br from-success/20 to-success/5 rounded-xl border border-success/30 p-6 hover:from-success/30 hover:to-success/10 transition-all hover-lift">
            <div class="w-12 h-12 rounded-xl bg-success/20 flex items-center justify-center mb-4 group-hover:bg-success/30 transition-colors">
                <i data-lucide="graduation-cap" class="w-6 h-6 text-success"></i>
            </div>
            <h4 class="font-semibold text-foreground mb-1">Talabalar</h4>
            <p class="text-sm text-muted-foreground">Talabalar ma'lumotlari</p>
        </a>

        <a href="{{ route('export.index') }}" class="group bg-gradient-to-br from-accent/20 to-accent/5 rounded-xl border border-accent/30 p-6 hover:from-accent/30 hover:to-accent/10 transition-all hover-lift">
            <div class="w-12 h-12 rounded-xl bg-accent/20 flex items-center justify-center mb-4 group-hover:bg-accent/30 transition-colors">
                <i data-lucide="download" class="w-6 h-6 text-accent"></i>
            </div>
            <h4 class="font-semibold text-foreground mb-1">Export</h4>
            <p class="text-sm text-muted-foreground">Excel hisobotlar</p>
        </a>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
// Dashboard Data
function dashboardData() {
    return {
        init() {
            // Real-time yangilash (har 30 sekundda)
            @if(auth()->user()->isAdmin())
            this.startRealTimeUpdates();
            @endif
        },
        startRealTimeUpdates() {
            // Darhol yangilash
            this.fetchRealTimeStats();

            // Har 30 sekundda yangilash
            setInterval(() => {
                this.fetchRealTimeStats();
            }, 30000);
        },
        async fetchRealTimeStats() {
            try {
                const response = await fetch('{{ route("dashboard.realtime") }}');
                const data = await response.json();

                // Kurs statistikasini yangilash
                if (data.kurslar) {
                    [1, 2].forEach(kurs => {
                        const stats = data.kurslar[kurs];
                        if (stats) {
                            document.getElementById(`kurs${kurs}Foiz`).textContent = stats.jami_foiz + '%';
                            document.getElementById(`kurs${kurs}Bor`).textContent = stats.jami_bor;
                            document.getElementById(`kurs${kurs}Yoq`).textContent = stats.jami_yoq;

                            const progress = document.getElementById(`kurs${kurs}Progress`);
                            if (progress) {
                                progress.style.width = stats.jami_foiz + '%';
                            }
                        }
                    });
                }

                // Vaqtni yangilash
                if (data.server_vaqt) {
                    document.getElementById('currentTime').textContent = data.server_vaqt;
                }

                // Oxirgi yangilangan vaqt
                document.getElementById('lastUpdated').textContent = 'Yangilandi: ' + data.server_vaqt;

            } catch (error) {
                console.error('Real-time statistikani yuklashda xatolik:', error);
            }
        }
    }
}

// Vaqtni yangilash (har sekundda)
function updateCurrentTime() {
    fetch('{{ route("dashboard.refresh") }}')
        .then(res => res.json())
        .then(data => {
            if (data.para_holati && data.para_holati.hozirgi_vaqt) {
                document.getElementById('currentTime').textContent = data.para_holati.hozirgi_vaqt;
            }
        })
        .catch(() => {});
}
setInterval(updateCurrentTime, 1000);

// Trend Chart
const ctx = document.getElementById('trendChart').getContext('2d');
const trendData = @json($kunlikTrend);

const gradient = ctx.createLinearGradient(0, 0, 0, 320);
gradient.addColorStop(0, 'rgba(8, 145, 178, 0.3)');
gradient.addColorStop(1, 'rgba(8, 145, 178, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: trendData.map(d => d.kun + '\n' + d.sana),
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
            pointBorderWidth: 3,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointHoverBackgroundColor: '#6366f1',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'var(--card)',
                titleColor: 'var(--foreground)',
                bodyColor: 'var(--muted-foreground)',
                borderColor: 'rgba(8, 145, 178, 0.3)',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 },
                callbacks: {
                    label: function(context) {
                        return 'Davomat: ' + context.parsed.y + '%';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    },
                    color: 'var(--muted-foreground)',
                    font: { size: 12 }
                },
                grid: {
                    color: 'rgba(8, 145, 178, 0.1)',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: 'var(--muted-foreground)',
                    font: { size: 12 }
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Lucide icons qayta yuklash
setTimeout(() => { lucide.createIcons(); }, 100);

// Guruh Search va Filter
const searchInput = document.getElementById('guruhSearch');
const filterSelect = document.getElementById('guruhFilter');
const guruhCards = document.querySelectorAll('.guruh-card');
const noResults = document.getElementById('noResults');

function filterGuruhlar() {
    const searchTerm = searchInput.value.toLowerCase();
    const filterValue = filterSelect.value;
    let visibleCount = 0;

    guruhCards.forEach(card => {
        const guruhName = card.dataset.guruh;
        const kurs = parseInt(card.dataset.kurs);
        const foiz = parseInt(card.dataset.foiz);

        let matchesSearch = guruhName.includes(searchTerm);
        let matchesFilter = true;

        if (filterValue === 'kurs1') matchesFilter = kurs === 1;
        else if (filterValue === 'kurs2') matchesFilter = kurs === 2;
        else if (filterValue === 'high') matchesFilter = foiz >= 80;
        else if (filterValue === 'medium') matchesFilter = foiz >= 60 && foiz < 80;
        else if (filterValue === 'low') matchesFilter = foiz < 60;

        if (matchesSearch && matchesFilter) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    if (noResults) {
        noResults.classList.toggle('hidden', visibleCount > 0);
    }
}

if (searchInput) searchInput.addEventListener('input', filterGuruhlar);
if (filterSelect) filterSelect.addEventListener('change', filterGuruhlar);
</script>
@endpush
