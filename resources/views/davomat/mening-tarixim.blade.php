@extends('layouts.app')

@section('title', 'Mening Davomat Tarixim - Kollej Davomat Tizimi')
@section('page-title', 'Mening Davomat Tarixim')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="filter" class="w-5 h-5 text-primary"></i>
            <h3 class="font-semibold text-foreground">Filtrlash</h3>
        </div>
        <form action="{{ route('davomat.mening-tarixim') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Sana (dan)</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="date" name="sana_dan" value="{{ request('sana_dan') }}" class="input pl-10">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Sana (gacha)</label>
                <div class="relative">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="date" name="sana_gacha" value="{{ request('sana_gacha') }}" class="input pl-10">
                </div>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn btn-primary w-full gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Qidirish
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Sana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Talaba</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Guruh</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">1-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">2-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">3-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">4-para</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($davomatlar as $davomat)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-muted-foreground"></i>
                            {{ $davomat->sana->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 flex-shrink-0 bg-primary/10 rounded-full flex items-center justify-center">
                                    <span class="text-primary font-semibold text-sm">{{ strtoupper(substr($davomat->talaba?->fish ?? 'N', 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $davomat->talaba?->fish ?? 'Noma\'lum' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-secondary">{{ $davomat->guruh?->nomi ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_1 === 'bor')
                            <span class="badge badge-success gap-1"><i data-lucide="check" class="w-3 h-3"></i> Bor</span>
                            @elseif($davomat->para_1 === 'yoq')
                            <span class="badge badge-destructive gap-1"><i data-lucide="x" class="w-3 h-3"></i> Yo'q</span>
                            @else
                            <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_2 === 'bor')
                            <span class="badge badge-success gap-1"><i data-lucide="check" class="w-3 h-3"></i> Bor</span>
                            @elseif($davomat->para_2 === 'yoq')
                            <span class="badge badge-destructive gap-1"><i data-lucide="x" class="w-3 h-3"></i> Yo'q</span>
                            @else
                            <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_3 === 'bor')
                            <span class="badge badge-success gap-1"><i data-lucide="check" class="w-3 h-3"></i> Bor</span>
                            @elseif($davomat->para_3 === 'yoq')
                            <span class="badge badge-destructive gap-1"><i data-lucide="x" class="w-3 h-3"></i> Yo'q</span>
                            @else
                            <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($davomat->para_4 === 'bor')
                            <span class="badge badge-success gap-1"><i data-lucide="check" class="w-3 h-3"></i> Bor</span>
                            @elseif($davomat->para_4 === 'yoq')
                            <span class="badge badge-destructive gap-1"><i data-lucide="x" class="w-3 h-3"></i> Yo'q</span>
                            @else
                            <span class="text-muted-foreground">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-lucide="clipboard-x" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
                            <p class="text-muted-foreground">Siz hali davomat olmadingiz</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border">
            {{ $davomatlar->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
