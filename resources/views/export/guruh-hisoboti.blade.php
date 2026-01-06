@extends('layouts.app')

@section('title', $guruh->nomi . ' Hisoboti - Kollej Davomat Tizimi')
@section('page-title', $guruh->nomi . ' - Oylik Hisobot')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-foreground">{{ $guruh->nomi }}</h2>
                    <p class="text-muted-foreground">{{ $guruh->yunalish }} | {{ $guruh->kurs }}-kurs</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
                <form action="{{ route('export.guruh', $guruh) }}" method="GET" class="flex flex-wrap gap-2">
                    <select name="oy" class="input w-auto">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $oy == $i ? 'selected' : '' }}>{{ $i }}-oy</option>
                        @endfor
                    </select>
                    <select name="yil" class="input w-auto">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $yil == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="btn btn-primary gap-2">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        Ko'rsatish
                    </button>
                </form>
                <a href="{{ route('export.guruh', ['guruh' => $guruh, 'oy' => $oy, 'yil' => $yil, 'export' => 1]) }}"
                   class="btn btn-success gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Excel yuklash
                </a>
            </div>
        </div>
    </div>

    <!-- Statistika Table -->
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border bg-gradient-to-r from-primary/10 to-secondary/10">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                        <i data-lucide="calendar-range" class="w-5 h-5 text-primary"></i>
                        {{ $yil }}-yil {{ $oy }}-oy davomati
                    </h3>
                    <p class="text-sm text-muted-foreground mt-1">Talabalar davomat statistikasi va foizlar</p>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-sm">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-success"></span>
                        <span class="text-muted-foreground">&ge;90% - A'lo</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-accent"></span>
                        <span class="text-muted-foreground">70-89% - O'rta</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-destructive"></span>
                        <span class="text-muted-foreground">&lt;70% - Kam</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase">FISH</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase">
                            <span class="flex items-center justify-center gap-1">
                                <i data-lucide="check" class="w-4 h-4 text-success"></i>
                                Bor
                            </span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase">
                            <span class="flex items-center justify-center gap-1">
                                <i data-lucide="x" class="w-4 h-4 text-destructive"></i>
                                Yo'q
                            </span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase">Jami</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase">
                            <span class="flex items-center justify-center gap-1">
                                <i data-lucide="percent" class="w-4 h-4"></i>
                                Foiz
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($hisobot as $index => $item)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 flex-shrink-0 bg-primary/10 rounded-full flex items-center justify-center">
                                    <span class="text-primary font-semibold text-sm">{{ strtoupper(substr($item['talaba']->fish, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $item['talaba']->fish }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-success font-semibold">{{ $item['bor'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="text-destructive font-semibold">{{ $item['yoq'] }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-foreground font-medium">
                            {{ $item['bor'] + $item['yoq'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($item['foiz'] >= 90)
                            <span class="badge badge-success">{{ $item['foiz'] }}%</span>
                            @elseif($item['foiz'] >= 70)
                            <span class="badge" style="background: var(--accent); color: white;">{{ $item['foiz'] }}%</span>
                            @else
                            <span class="badge badge-destructive">{{ $item['foiz'] }}%</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="file-x" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
                            <p class="text-muted-foreground">Bu oy uchun davomat ma'lumotlari yo'q</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Back Button -->
    <div>
        <a href="{{ route('export.index') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary/80 transition-colors font-medium">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Export sahifasiga qaytish
        </a>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
