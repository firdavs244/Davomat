@extends('layouts.app')

@section('title', $guruh->nomi . ' - Kollej Davomat Tizimi')
@section('page-title', $guruh->nomi)

@section('content')
<div class="space-y-6">
    <!-- Guruh Info Card -->
    <div class="bg-card rounded-xl border border-border p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-primary"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-foreground">{{ $guruh->nomi }}</h2>
                    <p class="text-muted-foreground flex items-center gap-2">
                        <i data-lucide="bookmark" class="w-4 h-4"></i>
                        {{ $guruh->yunalish }} | {{ $guruh->kurs }}-kurs
                    </p>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-3">
                <span class="badge {{ $guruh->is_active ? 'badge-success' : 'badge-muted' }} text-sm px-3 py-1">
                    {{ $guruh->is_active ? 'Aktiv' : 'Noaktiv' }}
                </span>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('guruhlar.edit', $guruh->id) }}" class="btn btn-primary gap-2">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Tahrirlash
                </a>
                @endif
            </div>
        </div>

        <!-- Statistika -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-primary/10 rounded-xl p-4 text-center border border-primary/20">
                <i data-lucide="user-check" class="w-6 h-6 text-primary mx-auto mb-2"></i>
                <p class="text-2xl font-bold text-primary">{{ $guruh->talabalar->where('holati', 'aktiv')->count() }}</p>
                <p class="text-sm text-primary/80">Aktiv talabalar</p>
            </div>
            <div class="bg-muted/50 rounded-xl p-4 text-center border border-border">
                <i data-lucide="user-x" class="w-6 h-6 text-muted-foreground mx-auto mb-2"></i>
                <p class="text-2xl font-bold text-muted-foreground">{{ $guruh->talabalar->where('holati', 'noaktiv')->count() }}</p>
                <p class="text-sm text-muted-foreground">Noaktiv talabalar</p>
            </div>
            <div class="bg-success/10 rounded-xl p-4 text-center border border-success/20">
                <i data-lucide="percent" class="w-6 h-6 text-success mx-auto mb-2"></i>
                <p class="text-2xl font-bold text-success">{{ $guruh->bugungi_statistika['foiz'] }}%</p>
                <p class="text-sm text-success/80">Bugungi davomat</p>
            </div>
            <div class="bg-secondary/10 rounded-xl p-4 text-center border border-secondary/20">
                <i data-lucide="users" class="w-6 h-6 text-secondary mx-auto mb-2"></i>
                <p class="text-2xl font-bold text-secondary">{{ $guruh->talabalar->count() }}</p>
                <p class="text-sm text-secondary/80">Jami talabalar</p>
            </div>
        </div>
    </div>

    <!-- Talabalar ro'yxati -->
    <div class="bg-card rounded-xl border border-border overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <i data-lucide="list" class="w-5 h-5 text-primary"></i>
                Talabalar ro'yxati
            </h3>
            <span class="badge badge-secondary">{{ $guruh->talabalar->count() }} ta</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">FISH</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Kirgan sana</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Holati</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Amallar</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($guruh->talabalar as $index => $talaba)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-sm font-medium">
                                    {{ strtoupper(substr($talaba->fish, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $talaba->fish }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">{{ $talaba->kirgan_sana->format('d.m.Y') }}</td>
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
                        <td colspan="5" class="px-6 py-12 text-center">
                            <i data-lucide="users" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
                            <p class="text-muted-foreground">Bu guruhda talabalar yo'q</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
