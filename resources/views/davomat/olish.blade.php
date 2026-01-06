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

    <!-- Filter Section -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <i data-lucide="filter" class="w-5 h-5 text-primary"></i>
                <h3 class="text-lg font-heading font-semibold text-foreground">Parametrlarni Tanlang</h3>
            </div>
            <span class="badge badge-primary">
                <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                {{ now()->format('d.m.Y') }}
            </span>
        </div>

        <form action="{{ route('davomat.olish') }}" method="GET" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                        <option value="1" {{ $para == 1 ? 'selected' : '' }}>1-para (08:00 - 09:20)</option>
                        <option value="2" {{ $para == 2 ? 'selected' : '' }}>2-para (09:30 - 10:50)</option>
                        <option value="3" {{ $para == 3 ? 'selected' : '' }}>3-para (11:00 - 12:20)</option>
                    </select>
                </div>

                <!-- Guruh - Searchable -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-foreground">
                        <i data-lucide="users" class="w-4 h-4 inline mr-1"></i>
                        Guruh
                    </label>
                    <div class="relative" x-data="{
                        open: false,
                        search: '',
                        selected: '{{ $guruhId ? $guruhlar->find($guruhId)?->nomi : '' }}',
                        selectedId: '{{ $guruhId }}',
                        get filteredGuruhlar() {
                            if (!this.search) return this.guruhlar;
                            return this.guruhlar.filter(g =>
                                g.nomi.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        guruhlar: {{ Js::from($guruhlar->map(fn($g) => ['id' => $g->id, 'nomi' => $g->nomi, 'kurs' => $g->kurs])) }}
                    }">
                        <input type="hidden" name="guruh_id" :value="selectedId">

                        <!-- Search Input -->
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                            <input type="text"
                                   x-model="search"
                                   @focus="open = true"
                                   @click="open = true"
                                   :placeholder="selected || 'Guruh izlash...'"
                                   :class="selected ? 'text-foreground' : 'text-muted-foreground'"
                                   class="input pl-10 pr-10">
                            <button type="button"
                                    x-show="selected"
                                    @click="selected = ''; selectedId = ''; search = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-destructive transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <!-- Dropdown -->
                        <div x-show="open"
                             x-transition
                             @click.away="open = false; search = ''"
                             class="absolute z-50 w-full mt-2 bg-card border border-border rounded-xl shadow-lg max-h-64 overflow-y-auto">
                            <div class="p-2">
                                <template x-if="filteredGuruhlar.length === 0">
                                    <div class="px-4 py-3 text-center text-muted-foreground">
                                        <i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2"></i>
                                        <p class="text-sm">Guruh topilmadi</p>
                                    </div>
                                </template>
                                <template x-for="guruh in filteredGuruhlar" :key="guruh.id">
                                    <button type="button"
                                            @click="selected = guruh.nomi; selectedId = guruh.id; open = false; search = ''"
                                            :class="selectedId == guruh.id ? 'bg-primary/20 text-primary' : 'text-foreground hover:bg-muted/50'"
                                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition-colors">
                                        <span class="font-medium" x-text="guruh.nomi"></span>
                                        <span class="text-xs text-muted-foreground" x-text="guruh.kurs + '-kurs'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-muted-foreground">{{ $guruhlar->count() }} ta guruh mavjud</p>
                </div>

                <!-- Button -->
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full gap-2">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        Ko'rsatish
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($guruhId && $talabalar->count() > 0)
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
                </div>
            </div>
        </div>

        <form action="{{ route('davomat.saqlash') }}" method="POST" id="davomatForm" class="p-6">
            @csrf
            <input type="hidden" name="guruh_id" value="{{ $guruhId }}">
            <input type="hidden" name="sana" value="{{ $sana }}">
            <input type="hidden" name="para" value="{{ $para }}">

            <!-- Quick Actions & Stats -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="selectAll('bor')"
                            class="inline-flex items-center px-4 py-2 bg-success/20 text-success border border-success/30 rounded-lg hover:bg-success/30 transition-colors text-sm font-medium gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Hammasi Bor
                    </button>
                    <button type="button" onclick="selectAll('yoq')"
                            class="inline-flex items-center px-4 py-2 bg-destructive/20 text-destructive border border-destructive/30 rounded-lg hover:bg-destructive/30 transition-colors text-sm font-medium gap-2">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Hammasi Yo'q
                    </button>
                    <button type="button" onclick="resetAll()"
                            class="inline-flex items-center px-4 py-2 bg-muted/50 text-muted-foreground border border-border rounded-lg hover:bg-muted transition-colors text-sm font-medium gap-2">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Tozalash
                    </button>
                </div>

                <!-- Live Stats -->
                <div class="flex items-center space-x-4 text-sm" id="liveStats">
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-success mr-2"></span>
                        <span class="text-foreground font-medium">Bor: <span id="borCount">0</span></span>
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-destructive mr-2"></span>
                        <span class="text-foreground font-medium">Yo'q: <span id="yoqCount">0</span></span>
                    </span>
                    <span class="flex items-center">
                        <span class="w-3 h-3 rounded-full bg-muted-foreground mr-2"></span>
                        <span class="text-muted-foreground">Qolgan: <span id="qolganCount">{{ $talabalar->count() }}</span></span>
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
                                   class="sr-only peer" onchange="updateStats()">
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
                                   class="sr-only peer" onchange="updateStats()">
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
                    Barcha talabalar uchun davomat belgilashni unutmang
                </p>
                <button type="submit" class="btn btn-primary px-8 py-3 gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Davomatni Saqlash
                </button>
            </div>
        </form>
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
            Davomat olish uchun yuqoridagi formadan sana, para va guruhni tanlang. Guruhni izlash uchun guruh nomini yozing.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <div class="flex items-center px-4 py-2 bg-muted/30 rounded-lg border border-border">
                <span class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center mr-3 text-primary font-bold">1</span>
                <span class="text-sm text-foreground">Sanani tanlang</span>
            </div>
            <div class="flex items-center px-4 py-2 bg-muted/30 rounded-lg border border-border">
                <span class="w-8 h-8 rounded-lg bg-secondary/20 flex items-center justify-center mr-3 text-secondary font-bold">2</span>
                <span class="text-sm text-foreground">Parani tanlang</span>
            </div>
            <div class="flex items-center px-4 py-2 bg-muted/30 rounded-lg border border-border">
                <span class="w-8 h-8 rounded-lg bg-accent/20 flex items-center justify-center mr-3 text-accent font-bold">3</span>
                <span class="text-sm text-foreground">Guruhni izlang</span>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function selectAll(value) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${value}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
    updateStats();
}

function resetAll() {
    const radios = document.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        radio.checked = false;
    });
    updateStats();
}

function updateStats() {
    const borCount = document.querySelectorAll('input[type="radio"][value="bor"]:checked').length;
    const yoqCount = document.querySelectorAll('input[type="radio"][value="yoq"]:checked').length;
    const totalCount = {{ $talabalar->count() ?? 0 }};
    const qolganCount = totalCount - borCount - yoqCount;

    document.getElementById('borCount').textContent = borCount;
    document.getElementById('yoqCount').textContent = yoqCount;
    document.getElementById('qolganCount').textContent = qolganCount;
}

// Initial stats update
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    setTimeout(() => { lucide.createIcons(); }, 100);
});

// Form validation
document.getElementById('davomatForm')?.addEventListener('submit', function(e) {
    const talabalar = document.querySelectorAll('input[type="radio"]');
    const names = new Set();
    let allSelected = true;

    talabalar.forEach(radio => names.add(radio.name));

    names.forEach(name => {
        const selected = document.querySelector(`input[name="${name}"]:checked`);
        if (!selected) {
            allSelected = false;
        }
    });

    if (!allSelected) {
        if (!confirm("Ba'zi talabalar uchun davomat belgilanmagan!\n\nDavom etishni xohlaysizmi?")) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
@endsection
