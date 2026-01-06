@extends('layouts.app')

@section('title', 'Foydalanuvchilar - Kollej Davomat Tizimi')
@section('page-title', 'Foydalanuvchilar')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-muted-foreground">Tizim foydalanuvchilari</p>
        </div>
        <a href="{{ route('foydalanuvchilar.create') }}" class="btn btn-primary gap-2">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            Yangi Foydalanuvchi
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-card rounded-xl border border-border p-4">
        <form action="{{ route('foydalanuvchilar.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
                <input type="text" name="qidiruv" value="{{ request('qidiruv') }}" placeholder="Ism yoki email bo'yicha qidirish..."
                       class="input pl-10">
            </div>
            <div>
                <select name="role" class="input">
                    <option value="">Barcha rollar</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    <option value="davomat_oluvchi" {{ request('role') == 'davomat_oluvchi' ? 'selected' : '' }}>Davomat Oluvchi</option>
                    <option value="koruvchi" {{ request('role') == 'koruvchi' ? 'selected' : '' }}>Ko'ruvchi</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Ism</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Holati</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-muted-foreground uppercase tracking-wider">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($foydalanuvchilar as $index => $user)
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            {{ ($foydalanuvchilar->currentPage() - 1) * $foydalanuvchilar->perPage() + $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 flex-shrink-0 bg-primary/10 rounded-full flex items-center justify-center">
                                    <span class="text-primary font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm font-medium text-foreground">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge {{ $user->role === 'admin' ? 'badge-secondary' : ($user->role === 'davomat_oluvchi' ? 'badge-primary' : 'badge-muted') }}">
                                {{ $user->role_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-destructive' }}">
                                {{ $user->is_active ? 'Aktiv' : 'Noaktiv' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('foydalanuvchilar.edit', $user) }}" class="p-2 rounded-lg text-primary hover:bg-primary/10 transition-colors" title="Tahrirlash">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('foydalanuvchilar.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-lg {{ $user->is_active ? 'text-warning hover:bg-warning/10' : 'text-success hover:bg-success/10' }} transition-colors" title="{{ $user->is_active ? 'Bloklash' : 'Faollashtirish' }}">
                                        <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i data-lucide="users" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
                            <p class="text-muted-foreground">Foydalanuvchilar topilmadi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-border">
            {{ $foydalanuvchilar->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    setTimeout(() => { lucide.createIcons(); }, 100);
</script>
@endpush
@endsection
