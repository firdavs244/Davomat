@extends('layouts.app')

@section('title', 'Davomat Tarixi - Kollej Davomat Tizimi')
@section('page-title', 'Davomat Tarixi')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="filter" class="w-5 h-5 text-primary"></i>
            <h3 class="font-semibold text-foreground">Filtrlash</h3>
        </div>
        <form action="{{ route('davomat.tarixi') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Guruh</label>
                <select name="guruh_id" class="input">
                    <option value="">Barcha guruhlar</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}" {{ request('guruh_id') == $guruh->id ? 'selected' : '' }}>
                        {{ $guruh->nomi }}
                    </option>
                    @endforeach
                </select>
            </div>
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
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Talaba</label>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                    <input type="text" name="talaba" value="{{ request('talaba') }}" placeholder="Talaba nomi..." class="input pl-10">
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

    @php
        // Davomatlarni sana va talaba bo'yicha guruhlash
        $groupedData = [];
        foreach($davomatlar as $davomat) {
            $sana = $davomat->sana->format('Y-m-d');
            $talabaId = $davomat->talaba_id;

            if (!isset($groupedData[$sana])) {
                $groupedData[$sana] = [
                    'sana' => $davomat->sana,
                    'talabalar' => []
                ];
            }

            if (!isset($groupedData[$sana]['talabalar'][$talabaId])) {
                $groupedData[$sana]['talabalar'][$talabaId] = [
                    'talaba' => $davomat->talaba,
                    'guruh' => $davomat->guruh,
                    'davomat' => $davomat,
                    'xodim' => $davomat->xodim
                ];
            }
        }
    @endphp

    <!-- Results - Grouped by Date -->
    @forelse($groupedData as $sanaKey => $sanaData)
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <!-- Date Header -->
        <div class="bg-primary/10 px-6 py-3 border-b border-border flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="calendar" class="w-5 h-5 text-primary"></i>
                <span class="font-semibold text-foreground">{{ $sanaData['sana']->format('d.m.Y') }}</span>
                <span class="text-sm text-muted-foreground">({{ $sanaData['sana']->locale('uz')->dayName }})</span>
            </div>
            <span class="badge badge-secondary">{{ count($sanaData['talabalar']) }} ta talaba</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider w-8">№</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Talaba FISH</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Guruh</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">1-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">2-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">3-para</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">4-para</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Xodim</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Amallar</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @php $counter = 0; @endphp
                    @foreach($sanaData['talabalar'] as $talabaData)
                    @php $counter++; $davomat = $talabaData['davomat']; @endphp
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">{{ $counter }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 flex-shrink-0 bg-primary/10 rounded-full flex items-center justify-center">
                                    <span class="text-primary font-semibold text-sm">{{ strtoupper(substr($talabaData['talaba']?->fish ?? 'N', 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $talabaData['talaba']?->fish ?? 'Noma\'lum' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-secondary">{{ $talabaData['guruh']?->nomi ?? '-' }}</span>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            {{ $talabaData['xodim']?->name ?? '-' }}
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('davomat.edit', $davomat) }}" class="p-2 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Tahrirlash">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('davomat.destroy', $davomat) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-destructive hover:bg-destructive/10 transition-colors" title="O'chirish">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-card rounded-xl border border-border p-12 text-center">
        <i data-lucide="clipboard" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
        <p class="text-muted-foreground">Ma'lumot topilmadi</p>
    </div>
    @endforelse

    <!-- Pagination -->
    <div class="bg-card rounded-xl border border-border px-6 py-4">
        {{ $davomatlar->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
