@extends('layouts.app')

@section('title', 'Davomat Olish - Kollej Davomat Tizimi')
@section('page-title', 'Davomat Olish')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form action="{{ route('davomat.olish') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Sana -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sana</label>
                    <input type="date" name="sana" value="{{ $sana }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Para -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Para</label>
                    <select name="para" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ $para == 1 ? 'selected' : '' }}>1-para</option>
                        <option value="2" {{ $para == 2 ? 'selected' : '' }}>2-para</option>
                        <option value="3" {{ $para == 3 ? 'selected' : '' }}>3-para</option>
                    </select>
                </div>
                
                <!-- Guruh -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guruh</label>
                    <select name="guruh_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Guruh tanlang --</option>
                        @foreach($guruhlar as $guruh)
                        <option value="{{ $guruh->id }}" {{ $guruhId == $guruh->id ? 'selected' : '' }}>
                            {{ $guruh->nomi }} ({{ $guruh->kurs }}-kurs)
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Ko'rsatish
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    @if($guruhId && $talabalar->count() > 0)
    <!-- Davomat Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                📋 {{ $guruhlar->find($guruhId)->nomi }} - {{ $para }}-para davomati
            </h3>
            <span class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($sana)->format('d.m.Y') }}
            </span>
        </div>
        
        <form action="{{ route('davomat.saqlash') }}" method="POST" id="davomatForm">
            @csrf
            <input type="hidden" name="guruh_id" value="{{ $guruhId }}">
            <input type="hidden" name="sana" value="{{ $sana }}">
            <input type="hidden" name="para" value="{{ $para }}">
            
            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" onclick="selectAll('bor')" 
                        class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                    ✓ Hammasi Bor
                </button>
                <button type="button" onclick="selectAll('yoq')" 
                        class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm">
                    ✗ Hammasi Yo'q
                </button>
            </div>
            
            <!-- Talabalar ro'yxati -->
            <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                @foreach($talabalar as $index => $talaba)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <span class="w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full text-sm font-semibold">
                            {{ $index + 1 }}
                        </span>
                        <span class="ml-3 font-medium text-gray-800">{{ $talaba->fish }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="davomat[{{ $talaba->id }}]" value="bor" 
                                   {{ ($mavjudDavomat[$talaba->id] ?? '') == 'bor' ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300">
                            <span class="ml-2 text-green-600 font-medium">Bor</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="davomat[{{ $talaba->id }}]" value="yoq" 
                                   {{ ($mavjudDavomat[$talaba->id] ?? '') == 'yoq' ? 'checked' : '' }}
                                   class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <span class="ml-2 text-red-600 font-medium">Yo'q</span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Davomatni Saqlash
                </button>
            </div>
        </form>
    </div>
    @elseif($guruhId)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <p class="text-yellow-700">Bu guruhda aktiv talabalar yo'q yoki tanlangan sanada davomat olib bo'lmaydi.</p>
    </div>
    @else
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
        <svg class="w-12 h-12 text-blue-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-blue-700">Davomat olish uchun sana, para va guruhni tanlang.</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function selectAll(value) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${value}"]`);
    radios.forEach(radio => radio.checked = true);
}

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
        if (!confirm("Ba'zi talabalar uchun davomat belgilanmagan. Davom etishni xohlaysizmi?")) {
            e.preventDefault();
        }
    }
});
</script>
@endpush
