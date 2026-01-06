@extends('layouts.app')

@section('title', 'Guruhlar - Kollej Davomat Tizimi')
@section('page-title', 'Guruhlar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-muted-foreground">Barcha guruhlar ro'yxati</p>
        </div>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('guruhlar.create') }}"
           class="btn btn-primary gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Yangi Guruh
        </a>
        @endif
    </div>

    <!-- Filter -->
    <div class="bg-card rounded-xl border border-border p-4">
        <form action="{{ route('guruhlar.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                <input type="text" name="qidiruv" value="{{ request('qidiruv') }}" placeholder="Qidirish..."
                       class="input pl-10">
            </div>
            <div>
                <select name="kurs" class="input">
                    <option value="">Barcha kurslar</option>
                    @foreach($kurslar as $kurs)
                    <option value="{{ $kurs }}" {{ request('kurs') == $kurs ? 'selected' : '' }}>{{ $kurs }}-kurs</option>
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

    <!-- Guruhlar Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($guruhlar as $guruh)
        <div class="bg-card rounded-xl border border-border p-6 hover-lift transition-all">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">{{ $guruh->nomi }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $guruh->yunalish }}</p>
                </div>
                <span class="badge {{ $guruh->is_active ? 'badge-success' : 'badge-muted' }}">
                    {{ $guruh->is_active ? 'Aktiv' : 'Noaktiv' }}
                </span>
            </div>

            <div class="mt-4 flex items-center justify-between text-sm">
                <span class="text-muted-foreground flex items-center gap-1">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    {{ $guruh->aktiv_talabalar_count }} talaba
                </span>
                <span class="badge badge-primary">
                    {{ $guruh->kurs }}-kurs
                </span>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="mt-4 pt-4 border-t border-border flex justify-end space-x-2">
                <a href="{{ route('guruhlar.show', $guruh->id) }}" class="p-2 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors" title="Ko'rish">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                </a>
                <a href="{{ route('guruhlar.edit', $guruh->id) }}" class="p-2 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Tahrirlash">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                </a>
                @if($guruh->canBeDeleted())
                <form action="{{ route('guruhlar.destroy', $guruh->id) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan o\'chirmoqchimisiz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg text-destructive hover:bg-destructive/10 transition-colors" title="O'chirish">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full bg-muted/30 rounded-xl p-12 text-center border border-border">
            <i data-lucide="users" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
            <p class="text-muted-foreground">Guruhlar topilmadi</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $guruhlar->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
