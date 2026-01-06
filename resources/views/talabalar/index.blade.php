@extends('layouts.app')

@section('title', 'Talabalar - Kollej Davomat Tizimi')
@section('page-title', 'Talabalar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-muted-foreground">Barcha talabalar ro'yxati</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('talabalar.create') }}" class="btn btn-primary gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Yangi Talaba
        </a>
        @endif
    </div>

    <!-- Filter -->
    <div class="bg-card rounded-xl border border-border p-4">
        <form action="{{ route('talabalar.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                <input type="text" name="qidiruv" value="{{ request('qidiruv') }}" placeholder="FISH bo'yicha qidirish..."
                       class="input pl-10">
            </div>
            <div>
                <select name="guruh_id" class="input">
                    <option value="">Barcha guruhlar</option>
                    @foreach($guruhlar as $guruh)
                    <option value="{{ $guruh->id }}" {{ request('guruh_id') == $guruh->id ? 'selected' : '' }}>{{ $guruh->nomi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="holat" class="input">
                    <option value="">Barcha holatlar</option>
                    <option value="aktiv" {{ request('holat') == 'aktiv' ? 'selected' : '' }}>Aktiv</option>
                    <option value="noaktiv" {{ request('holat') == 'noaktiv' ? 'selected' : '' }}>Noaktiv</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-outline w-full gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Qidirish
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">FISH</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Guruh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Kirgan sana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Holati</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Amallar</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($talabalar as $index => $talaba)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            {{ ($talabalar->currentPage() - 1) * $talabalar->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 flex-shrink-0 bg-primary/10 rounded-full flex items-center justify-center">
                                    <span class="text-primary font-semibold">{{ strtoupper(substr($talaba->fish, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $talaba->fish }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-primary">
                                {{ $talaba->guruh?->nomi ?? 'Guruhsiz' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            {{ $talaba->kirgan_sana->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge {{ $talaba->holati === 'aktiv' ? 'badge-success' : 'badge-muted' }}">
                                {{ $talaba->holat_nomi }}
                            </span>
                        </td>
                        @if(auth()->user()->isAdmin())
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('talabalar.show', $talaba) }}" class="p-2 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors" title="Ko'rish">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('talabalar.edit', $talaba) }}" class="p-2 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Tahrirlash">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="users" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
                            <p class="text-muted-foreground">Talabalar topilmadi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border">
            {{ $talabalar->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
