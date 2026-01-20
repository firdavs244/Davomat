@extends('layouts.app')

@section('title', 'Davomat Olish - Kollej Davomat Tizimi')
@section('page-title', 'Davomat Olish')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header -->
    <div class="relative overflow-hidden bg-gradient-to-r from-primary/20 via-secondary/20 to-accent/20 rounded-xl border border-border p-6">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center">
                    <i data-lucide="clipboard-check" class="w-5 h-5 text-primary"></i>
                </div>
                <h1 class="text-2xl font-heading font-bold text-foreground">Davomat Olish</h1>
            </div>
            <p class="text-muted-foreground">
                Talabalar davomatini belgilash va kuzatish
            </p>
        </div>
        <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-br from-primary/10 to-transparent rounded-full -translate-y-1/2 translate-x-1/2"></div>
    </div>

    @if(isset($xabar))
    <!-- Vaqt kutish xabari -->
    <div class="bg-accent/10 border border-accent/30 rounded-xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-accent/20 flex items-center justify-center">
            <i data-lucide="clock" class="w-8 h-8 text-accent"></i>
        </div>
        <h3 class="text-lg font-heading font-semibold text-foreground mb-2">{{ $xabar }}</h3>
        @if(isset($keyingiTugash))
        <div class="mt-4" x-data="paraTimer()" x-init="startTimer()">
            <p class="text-muted-foreground mb-2">Keyingi para tugashiga:</p>
            <div class="text-3xl font-bold text-primary" x-text="timeLeft"></div>
            <p class="text-sm text-muted-foreground mt-2">Sahifa avtomatik yangilanadi</p>
        </div>
        @endif
        <div class="mt-6 p-4 bg-muted/30 rounded-lg">
            <h4 class="font-medium text-foreground mb-2">Para vaqtlari:</h4>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div class="text-center">
                    <span class="badge {{ ($paraHolati['hozirgi_para'] ?? 0) == 1 ? 'badge-primary' : 'badge-secondary' }}">1-para</span>
                    <p class="mt-1 text-muted-foreground">08:00 - 09:20</p>
                </div>
                <div class="text-center">
                    <span class="badge {{ ($paraHolati['hozirgi_para'] ?? 0) == 2 ? 'badge-primary' : 'badge-secondary' }}">2-para</span>
                    <p class="mt-1 text-muted-foreground">09:30 - 10:50</p>
                </div>
                <div class="text-center">
                    <span class="badge {{ ($paraHolati['hozirgi_para'] ?? 0) == 3 ? 'badge-primary' : 'badge-secondary' }}">3-para</span>
                    <p class="mt-1 text-muted-foreground">11:00 - 12:20</p>
                </div>
            </div>
        </div>
    </div>
    @else

    <!-- Hozirgi vaqt va para holati -->
    <div class="bg-card rounded-xl border border-border p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-primary"></i>
                    <span class="font-medium text-foreground">Hozirgi vaqt:</span>
                    <span class="text-primary font-bold" id="serverTime">{{ $paraHolati['hozirgi_vaqt'] ?? '--:--:--' }}</span>
                </div>
                <div class="h-6 w-px bg-border"></div>
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-secondary"></i>
                    <span class="text-foreground">{{ now()->format('d.m.Y') }}</span>
                </div>
            </div>
            @if($para)
            <div class="flex items-center gap-2">
                <span class="badge badge-primary text-lg px-4 py-2">
                    <i data-lucide="book-open" class="w-4 h-4 mr-2"></i>
                    {{ $para }}-para davomati
                </span>
                @if(!$isAdmin)
                <span class="badge badge-accent">
                    <i data-lucide="lock" class="w-3 h-3 mr-1"></i>
                    Avtomatik tanlangan
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <i data-lucide="filter" class="w-5 h-5 text-primary"></i>
                <h3 class="text-lg font-heading font-semibold text-foreground">
                    @if($isAdmin)
                        Parametrlarni Tanlang
                    @else
                        Guruh Tanlang
                    @endif
                </h3>
            </div>
            @if(!$isAdmin && count($mavjudParalar ?? []) > 0)
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <i data-lucide="info" class="w-4 h-4"></i>
                Tugagan paralar: {{ implode(', ', array_map(fn($p) => $p . '-para', $mavjudParalar)) }}
            </div>
            @endif
        </div>

        <form action="{{ route('davomat.olish') }}" method="GET" class="space-y-6" id="filterForm">
            @if($isAdmin)
            {{-- Admin uchun sana va para tanlash --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Sana -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-foreground">
                        <i data-lucide="calendar-days" class="w-4 h-4 inline mr-1"></i>
                        Sana
                    </label>
                    <input type="date" name="sana" value="{{ $sana }}" class="input">
                </div>

                <!-- Para -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-foreground">
                        <i data-lucide="clock" class="w-4 h-4 inline mr-1"></i>
                        Para
                    </label>
                    <select name="para" class="input">
                        @foreach($paralarRoyxati as $paraNum => $paraNomi)
                        <option value="{{ $paraNum }}" {{ $para == $paraNum ? 'selected' : '' }}>{{ $paraNomi }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Guruh search (admin) -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-foreground">
                        <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                        Guruh
                    </label>
                    <select name="guruh_id" class="input">
                        <option value="">-- Guruh tanlang --</option>
                        @foreach($guruhlar->groupBy('kurs') as $kurs => $kursGuruhlar)
                        <optgroup label="{{ $kurs }}-kurs">
                            @foreach($kursGuruhlar as $g)
                            <option value="{{ $g->id }}" {{ $guruhId == $g->id ? 'selected' : '' }}>{{ $g->nomi }}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary gap-2">
                <i data-lucide="search" class="w-5 h-5"></i>
                Ko'rsatish
            </button>
            @else
            {{-- Davomat oluvchi uchun - kurs tanlash, keyin guruhlar ro'yxati --}}
            <div x-data="kursGuruhSelector()">
                {{-- 1. Kurs tanlash --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-foreground mb-3">
                        <i data-lucide="graduation-cap" class="w-4 h-4 inline mr-1"></i>
                        Kursni tanlang
                    </label>
                    <div class="flex gap-4">
                        <button type="button" @click="selectKurs(1)"
                                :class="selectedKurs === 1 ? 'bg-primary text-primary-foreground border-primary shadow-lg scale-105' : 'bg-muted/50 text-muted-foreground border-border hover:bg-muted'"
                                class="flex-1 py-6 px-8 rounded-xl border-2 font-bold text-xl transition-all duration-200">
                            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2"></i>
                            1-kurs
                            <p class="text-sm font-normal mt-1" x-text="kurs1Count + ' guruh'"></p>
                        </button>
                        <button type="button" @click="selectKurs(2)"
                                :class="selectedKurs === 2 ? 'bg-secondary text-secondary-foreground border-secondary shadow-lg scale-105' : 'bg-muted/50 text-muted-foreground border-border hover:bg-muted'"
                                class="flex-1 py-6 px-8 rounded-xl border-2 font-bold text-xl transition-all duration-200">
                            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2"></i>
                            2-kurs
                            <p class="text-sm font-normal mt-1" x-text="kurs2Count + ' guruh'"></p>
                        </button>
                    </div>
                </div>

                {{-- 2. Guruhlar ro'yxati --}}
                <div x-show="selectedKurs" x-transition class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-foreground">
                            <i data-lucide="list" class="w-4 h-4 inline mr-1"></i>
                            <span x-text="selectedKurs + '-kurs guruhlari'"></span>
                        </label>
                        {{-- Search (qo'shimcha) --}}
                        <div class="relative w-64">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input type="text" x-model="searchQuery" placeholder="Qidirish..."
                                   class="input pl-10 py-2 text-sm">
                        </div>
                    </div>

                    <input type="hidden" name="guruh_id" x-model="selectedGuruhId">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 max-h-80 overflow-y-auto p-1">
                        <template x-for="guruh in filteredGuruhlar" :key="guruh.id">
                            <button type="button" @click="selectGuruh(guruh)"
                                    :class="[
                                        selectedGuruhId == guruh.id
                                            ? 'bg-primary/20 border-primary text-primary ring-2 ring-primary/30'
                                            : 'bg-card border-border hover:border-primary/50 hover:bg-primary/5',
                                        guruh.davomat_olingan ? 'opacity-60' : ''
                                    ]"
                                    class="flex items-center justify-between p-4 rounded-xl border-2 transition-all text-left">
                                <div>
                                    <span class="font-semibold text-foreground block" x-text="guruh.nomi"></span>
                                    <span class="text-xs text-muted-foreground" x-text="guruh.talabalar_soni + ' talaba'"></span>
                                </div>
                                <template x-if="guruh.davomat_olingan">
                                    <span class="badge badge-success text-xs">
                                        <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                        Olingan
                                    </span>
                                </template>
                                <template x-if="selectedGuruhId == guruh.id && !guruh.davomat_olingan">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-primary"></i>
                                </template>
                            </button>
                        </template>
                    </div>

                    <template x-if="filteredGuruhlar.length === 0 && searchQuery">
                        <div class="text-center py-8 text-muted-foreground">
                            <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2"></i>
                            <p>"<span x-text="searchQuery"></span>" bo'yicha guruh topilmadi</p>
                        </div>
                    </template>
                </div>

                {{-- 3. Ko'rsatish button --}}
                <div x-show="selectedGuruhId" x-transition>
                    <button type="submit" class="btn btn-primary w-full py-3 text-lg gap-2">
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        Davomatni Ko'rsatish
                    </button>
                </div>
            </div>
            @endif
        </form>
    </div>

    @if(isset($davomatOlingan) && $davomatOlingan)
    <!-- Davomat allaqachon olingan -->
    <div class="bg-success/10 border border-success/30 rounded-xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-success/20 flex items-center justify-center">
            <i data-lucide="check-circle" class="w-8 h-8 text-success"></i>
        </div>
        <h3 class="text-lg font-heading font-semibold text-foreground mb-2">Davomat allaqachon olingan</h3>
        <p class="text-muted-foreground">
            Bu guruh uchun {{ $para }}-para davomati allaqachon olingan.
            Bir marta olingan davomatni o'zgartirib bo'lmaydi.
        </p>
        <a href="{{ route('davomat.mening-tarixim') }}" class="btn btn-secondary mt-4 gap-2">
            <i data-lucide="history" class="w-4 h-4"></i>
            Mening tarixim
        </a>
    </div>
    @elseif($guruhId && $talabalar->count() > 0)
    <!-- Davomat Form -->
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-6 py-4 border-b border-border">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-lg font-heading font-semibold text-foreground">
                        {{ $guruhlar->find($guruhId)->nomi }}
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        {{ $para }}-para • {{ \Carbon\Carbon::parse($sana)->format('d.m.Y') }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="badge badge-secondary">
                        {{ $talabalar->count() }} talaba
                    </span>
                    @if(!$isAdmin)
                    <span class="badge badge-accent">
                        <i data-lucide="shield-alert" class="w-3 h-3 mr-1"></i>
                        Bir martalik
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ route('davomat.saqlash') }}" method="POST" id="davomatForm" class="p-6"
              x-data="davomatForm()" @submit="handleSubmit($event)">
            @csrf
            <input type="hidden" name="guruh_id" value="{{ $guruhId }}">
            <input type="hidden" name="sana" value="{{ $sana }}">
            <input type="hidden" name="para" value="{{ $para }}">

            <!-- Quick Actions & Stats -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="selectAll('bor')"
                            class="inline-flex items-center px-4 py-2 bg-success/20 text-success border border-success/30 rounded-lg hover:bg-success/30 transition-colors text-sm font-medium gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Hammasi Bor
                    </button>
                    <button type="button" @click="selectAll('yoq')"
                            class="inline-flex items-center px-4 py-2 bg-destructive/20 text-destructive border border-destructive/30 rounded-lg hover:bg-destructive/30 transition-colors text-sm font-medium gap-2">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Hammasi Yo'q
                    </button>
                    <button type="button" @click="resetAll()"
                            class="inline-flex items-center px-4 py-2 bg-muted/50 text-muted-foreground border border-border rounded-lg hover:bg-muted transition-colors text-sm font-medium gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Tozalash
                    </button>
                </div>

                <!-- Live Stats -->
                <div class="flex items-center space-x-4 text-sm" id="liveStats">
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-success mr-2"></span>
                        <span class="text-foreground font-medium">Bor: <span x-text="borCount">0</span></span>
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-destructive mr-2"></span>
                        <span class="text-foreground font-medium">Yo'q: <span x-text="yoqCount">0</span></span>
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-muted-foreground mr-2"></span>
                        <span class="text-muted-foreground">Qolgan: <span x-text="qolganCount">{{ $talabalar->count() }}</span></span>
                    </span>
                </div>
            </div>

            <!-- Talabalar Ro'yxati -->
            <div class="space-y-2 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar" id="talabalarList">
                @foreach($talabalar as $index => $talaba)
                <div class="talaba-row group flex items-center justify-between p-4 bg-muted/20 rounded-xl border border-border hover:bg-muted/40 transition-all"
                     data-talaba-id="{{ $talaba->id }}">
                    <div class="flex items-center min-w-0">
                        <span class="w-10 h-10 flex items-center justify-center bg-primary/20 text-primary rounded-xl text-sm font-heading font-bold">
                            {{ $index + 1 }}
                        </span>
                        <div class="ml-4 min-w-0">
                            <p class="font-medium text-foreground truncate">{{ $talaba->fish }}</p>
                            <p class="text-xs text-muted-foreground">ID: {{ $talaba->id }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Bor Button -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="davomat[{{ $talaba->id }}]" value="bor"
                                   {{ ($mavjudDavomat[$talaba->id] ?? '') == 'bor' ? 'checked' : '' }}
                                   class="sr-only peer" @change="updateStats()">
                            <div class="w-20 py-2.5 text-center rounded-lg border-2 transition-all
                                        peer-checked:bg-success peer-checked:border-success peer-checked:text-white
                                        bg-muted/30 border-border text-muted-foreground hover:border-success/50 flex items-center justify-center gap-1">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span class="font-medium text-sm">Bor</span>
                            </div>
                        </label>

                        <!-- Yo'q Button -->
                        <label class="relative cursor-pointer">
                            <input type="radio" name="davomat[{{ $talaba->id }}]" value="yoq"
                                   {{ ($mavjudDavomat[$talaba->id] ?? '') == 'yoq' ? 'checked' : '' }}
                                   class="sr-only peer" @change="updateStats()">
                            <div class="w-20 py-2.5 text-center rounded-lg border-2 transition-all
                                        peer-checked:bg-destructive peer-checked:border-destructive peer-checked:text-white
                                        bg-muted/30 border-border text-muted-foreground hover:border-destructive/50 flex items-center justify-center gap-1">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                <span class="font-medium text-sm">Yo'q</span>
                            </div>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="mt-6 pt-6 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-muted-foreground flex items-center gap-1">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    @if(!$isAdmin)
                    <span class="text-warning">Diqqat: Saqlangandan keyin o'zgartirib bo'lmaydi!</span>
                    @else
                    Barcha talabalar uchun davomat belgilashni unutmang
                    @endif
                </p>
                <button type="submit" class="btn btn-primary px-8 py-3 gap-2" :disabled="submitting">
                    <i data-lucide="save" class="w-5 h-5" x-show="!submitting"></i>
                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin" x-show="submitting"></i>
                    <span x-text="submitting ? 'Saqlanmoqda...' : 'Davomatni Saqlash'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tasdiqlash Modal -->
    <div x-data="{ show: false }"
         x-show="show"
         x-on:open-confirm-modal.window="show = true"
         x-on:close-confirm-modal.window="show = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display: none;">
        <div class="bg-card rounded-2xl border border-border shadow-xl max-w-md w-full p-6"
             @click.away="show = false">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-warning/20 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-warning"></i>
                </div>
                <h3 class="text-xl font-heading font-bold text-foreground mb-2">Davomatni Tasdiqlang</h3>
                <p class="text-muted-foreground mb-6">
                    @if(!$isAdmin)
                    <span class="text-warning font-medium">Diqqat!</span> Bu davomatni saqlangandan keyin o'zgartirib bo'lmaydi.
                    @else
                    Davomatni saqlashni tasdiqlaysizmi?
                    @endif
                </p>

                <div class="bg-muted/30 rounded-lg p-4 mb-6 text-left">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-muted-foreground">Guruh:</span>
                        <span class="font-medium text-foreground">{{ $guruhlar->find($guruhId)?->nomi }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-muted-foreground">Para:</span>
                        <span class="font-medium text-foreground">{{ $para }}-para</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-muted-foreground">Sana:</span>
                        <span class="font-medium text-foreground">{{ \Carbon\Carbon::parse($sana)->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-muted-foreground">Talabalar:</span>
                        <span class="font-medium text-foreground">{{ $talabalar->count() }} ta</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="show = false" class="btn btn-secondary flex-1">
                        <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                        Bekor qilish
                    </button>
                    <button type="button" @click="$dispatch('confirm-save'); show = false" class="btn btn-primary flex-1">
                        <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                        Tasdiqlash
                    </button>
                </div>
            </div>
        </div>
    </div>

    @elseif($guruhId)
    <!-- Warning State -->
    <div class="bg-accent/10 border border-accent/30 rounded-xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-accent/20 flex items-center justify-center">
            <i data-lucide="alert-triangle" class="w-8 h-8 text-accent"></i>
        </div>
        <h3 class="text-lg font-heading font-semibold text-foreground mb-2">Talabalar topilmadi</h3>
        <p class="text-muted-foreground">Bu guruhda aktiv talabalar yo'q yoki tanlangan sanada davomat olib bo'lmaydi.</p>
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-card border border-border rounded-xl p-12 text-center">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
            <i data-lucide="clipboard-list" class="w-10 h-10 text-primary"></i>
        </div>
        <h3 class="text-xl font-heading font-semibold text-foreground mb-2">Davomat Olishni Boshlang</h3>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
            @if($isAdmin)
            Davomat olish uchun yuqoridagi formadan sana, para va guruhni tanlang.
            @else
            Davomat olish uchun yuqoridagi formadan guruhni tanlang. Para avtomatik tanlanadi.
            @endif
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            @if($isAdmin)
            <div class="flex items-center px-4 py-2 bg-muted/30 rounded-lg border border-border">
                <span class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center mr-3 text-primary font-bold">1</span>
                <span class="text-sm text-foreground">Sanani tanlang</span>
            </div>
            <div class="flex items-center px-4 py-2 bg-muted/30 rounded-lg border border-border">
                <span class="w-8 h-8 rounded-lg bg-secondary/20 flex items-center justify-center mr-3 text-secondary font-bold">2</span>
                <span class="text-sm text-foreground">Parani tanlang</span>
            </div>
            @endif
            <div class="flex items-center px-4 py-2 bg-muted/30 rounded-lg border border-border">
                <span class="w-8 h-8 rounded-lg bg-accent/20 flex items-center justify-center mr-3 text-accent font-bold">{{ $isAdmin ? '3' : '1' }}</span>
                <span class="text-sm text-foreground">Guruhni izlang</span>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
// Para timer (agar para tugamagan bo'lsa)
function paraTimer() {
    return {
        timeLeft: '--:--:--',
        interval: null,
        startTimer() {
            this.updateTime();
            this.interval = setInterval(() => {
                this.updateTime();
            }, 1000);
        },
        updateTime() {
            fetch('{{ route("davomat.para-holati") }}')
                .then(res => res.json())
                .then(data => {
                    if (data.keyingi_tugash) {
                        const now = new Date();
                        const [h, m, s] = data.keyingi_tugash.split(':');
                        const target = new Date();
                        target.setHours(parseInt(h), parseInt(m), parseInt(s), 0);

                        const diff = Math.max(0, Math.floor((target - now) / 1000));

                        if (diff <= 0) {
                            // Para tugadi, sahifani yangilash
                            clearInterval(this.interval);
                            window.location.reload();
                        } else {
                            const hours = Math.floor(diff / 3600);
                            const mins = Math.floor((diff % 3600) / 60);
                            const secs = diff % 60;
                            this.timeLeft = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                        }
                    }
                });
        }
    }
}

// Guruh qidiruv
// Kurs va guruh tanlash (davomat oluvchi uchun)
function kursGuruhSelector() {
    return {
        selectedKurs: null,
        selectedGuruhId: '{{ $guruhId ?? "" }}',
        searchQuery: '',
        guruhlar: @json($guruhlarJson ?? []),

        get kurs1Count() {
            return this.guruhlar.filter(g => g.kurs === 1).length;
        },

        get kurs2Count() {
            return this.guruhlar.filter(g => g.kurs === 2).length;
        },

        get filteredGuruhlar() {
            return this.guruhlar.filter(g => {
                const matchesKurs = g.kurs === this.selectedKurs;
                const matchesSearch = !this.searchQuery ||
                    g.nomi.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchesKurs && matchesSearch;
            });
        },

        init() {
            // Agar guruh tanlangan bo'lsa, uning kursini tanlash
            if (this.selectedGuruhId) {
                const guruh = this.guruhlar.find(g => g.id == this.selectedGuruhId);
                if (guruh) {
                    this.selectedKurs = guruh.kurs;
                }
            }

            // Davomat olingan guruhlarni tekshirish
            this.checkDavomatStatus();
        },

        async checkDavomatStatus() {
            try {
                const response = await fetch('{{ route("davomat.guruhlar-qidirish") }}');
                const data = await response.json();

                // Davomat olingan guruhlarni belgilash
                data.forEach(serverGuruh => {
                    const localGuruh = this.guruhlar.find(g => g.id === serverGuruh.id);
                    if (localGuruh) {
                        localGuruh.davomat_olingan = serverGuruh.davomat_olingan;
                    }
                });
            } catch (error) {
                console.error('Guruhlar holatini tekshirishda xatolik:', error);
            }
        },

        selectKurs(kurs) {
            this.selectedKurs = kurs;
            this.selectedGuruhId = '';
            this.searchQuery = '';
            this.$nextTick(() => lucide.createIcons());
        },

        selectGuruh(guruh) {
            if (guruh.davomat_olingan) {
                alert('Bu guruh uchun davomat allaqachon olingan!');
                return;
            }
            this.selectedGuruhId = guruh.id;
            this.$nextTick(() => lucide.createIcons());
        }
    }
}

// Davomat form
function davomatForm() {
    return {
        borCount: 0,
        yoqCount: 0,
        qolganCount: {{ $talabalar->count() ?? 0 }},
        totalCount: {{ $talabalar->count() ?? 0 }},
        submitting: false,
        confirmed: false,

        init() {
            this.updateStats();

            // Tasdiqlash hodisasini tinglash
            window.addEventListener('confirm-save', () => {
                this.confirmed = true;
                document.getElementById('davomatForm').submit();
            });
        },

        selectAll(value) {
            const radios = document.querySelectorAll(`input[type="radio"][value="${value}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
            this.updateStats();
        },

        resetAll() {
            const radios = document.querySelectorAll('input[type="radio"]');
            radios.forEach(radio => {
                radio.checked = false;
            });
            this.updateStats();
        },

        updateStats() {
            this.borCount = document.querySelectorAll('input[type="radio"][value="bor"]:checked').length;
            this.yoqCount = document.querySelectorAll('input[type="radio"][value="yoq"]:checked').length;
            this.qolganCount = this.totalCount - this.borCount - this.yoqCount;
        },

        handleSubmit(e) {
            e.preventDefault();

            // Agar tasdiqlangan bo'lsa, formni yuborish
            if (this.confirmed) {
                this.submitting = true;
                return true;
            }

            // Barcha talabalar uchun davomat belgilanganmi tekshirish
            if (this.qolganCount > 0) {
                if (!confirm(`${this.qolganCount} ta talaba uchun davomat belgilanmagan!\n\nDavom etishni xohlaysizmi?`)) {
                    return false;
                }
            }

            // Tasdiqlash modalni ochish
            window.dispatchEvent(new CustomEvent('open-confirm-modal'));
            return false;
        }
    }
}

// Server vaqtini yangilash
function updateServerTime() {
    fetch('{{ route("davomat.para-holati") }}')
        .then(res => res.json())
        .then(data => {
            const el = document.getElementById('serverTime');
            if (el) {
                el.textContent = data.hozirgi_vaqt;
            }
        });
}

// Har 1 sekundda vaqtni yangilash
setInterval(updateServerTime, 1000);

// Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => { lucide.createIcons(); }, 100);
});
</script>
@endpush
@endsection
