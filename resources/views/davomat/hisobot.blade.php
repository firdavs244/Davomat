@extends('layouts.app')

@section('title', 'Davomat Hisoboti')

@section('content')
<div class="space-y-6" x-data="hisobotApp()">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-7 h-7 text-indigo-600"></i>
                Davomat Hisoboti
            </h1>
            <p class="text-gray-500 mt-1">Qaysi guruhlardan davomat olindi, qaysilaridan olinmadi</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Hozirgi vaqt --}}
            <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
                <span class="text-sm text-gray-500">Hozirgi vaqt:</span>
                <span class="font-semibold text-indigo-600" x-text="hozirgiVaqt">{{ $hozirgiVaqt }}</span>
            </div>

            {{-- Sana tanlash --}}
            <form method="GET" class="flex items-center gap-2">
                <input type="date" name="sana" value="{{ $sana }}"
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    max="{{ date('Y-m-d') }}"
                    onchange="this.form.submit()">
            </form>
        </div>
    </div>

    {{-- Umumiy statistika kartalar --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ([1, 2, 3] as $para)
            @php $stat = $umumiyStatistika[$para]; @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 {{ $stat['tugadi'] ? '' : 'opacity-60' }}">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-lg text-gray-800">{{ $para }}-Para</h3>
                    @if($stat['tugadi'])
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                            Tugadi
                        </span>
                    @else
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">
                            Davom etmoqda
                        </span>
                    @endif
                </div>

                <div class="text-sm text-gray-500 mb-2">
                    {{ $paralar[$para]['boshlanish'] }} - {{ $paralar[$para]['tugash'] }}
                </div>

                @if($stat['tugadi'] || $stat['olingan'] > 0)
                    {{-- Guruhlar holati --}}
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-1">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600">Davomat olindi:</span>
                                <span class="font-semibold text-green-600">{{ $stat['olingan'] }}/{{ $stat['jami_guruh'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                                    style="width: {{ $stat['jami_guruh'] > 0 ? ($stat['olingan'] / $stat['jami_guruh']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Kelganlar/Kelmaganlar --}}
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $stat['jami_keldi'] }}</div>
                            <div class="text-xs text-green-600">Keldi</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $stat['jami_kelmadi'] }}</div>
                            <div class="text-xs text-red-600">Kelmadi</div>
                        </div>
                    </div>

                    {{-- Kurslar bo'yicha --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">1-kurs:</span>
                            <span class="font-semibold {{ $stat['kurs1_foiz'] >= 80 ? 'text-green-600' : ($stat['kurs1_foiz'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $stat['kurs1_keldi'] }}/{{ $stat['kurs1_jami'] }} ({{ $stat['kurs1_foiz'] }}%)
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">2-kurs:</span>
                            <span class="font-semibold {{ $stat['kurs2_foiz'] >= 80 ? 'text-green-600' : ($stat['kurs2_foiz'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $stat['kurs2_keldi'] }}/{{ $stat['kurs2_jami'] }} ({{ $stat['kurs2_foiz'] }}%)
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400">
                        <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2"></i>
                        <p>Para hali tugamadi</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Tab navigatsiyasi --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'guruhlar'"
                    :class="activeTab === 'guruhlar' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <i data-lucide="users" class="w-4 h-4 inline mr-2"></i>
                    Guruhlar bo'yicha
                </button>
                <button @click="activeTab = 'oluvchilar'"
                    :class="activeTab === 'oluvchilar' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                    <i data-lucide="user-check" class="w-4 h-4 inline mr-2"></i>
                    Davomat oluvchilar
                </button>
            </nav>
        </div>

        {{-- Guruhlar tab --}}
        <div x-show="activeTab === 'guruhlar'" class="p-4">
            {{-- Filter --}}
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <input type="text" x-model="qidiruv" placeholder="Guruh qidirish..."
                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">

                <select x-model="kursFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Barcha kurslar</option>
                    <option value="1">1-kurs</option>
                    <option value="2">2-kurs</option>
                </select>

                <select x-model="paraFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Barcha paralar</option>
                    <option value="1">1-para</option>
                    <option value="2">2-para</option>
                    <option value="3">3-para</option>
                </select>

                <select x-model="holatFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Barchasi</option>
                    <option value="olindi">Davomat olindi</option>
                    <option value="olinmadi">Davomat olinmadi</option>
                </select>
            </div>

            {{-- Guruhlar jadvali --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guruh</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kurs</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Talabalar</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">1-Para</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">2-Para</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">3-Para</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($guruhlarData as $data)
                            <tr class="hover:bg-gray-50 guruh-row"
                                data-guruh="{{ strtolower($data['guruh']->nomi) }}"
                                data-kurs="{{ $data['guruh']->kurs }}"
                                data-para1="{{ $data['paralar'][1]['olindi'] ? 'olindi' : 'olinmadi' }}"
                                data-para2="{{ $data['paralar'][2]['olindi'] ? 'olindi' : 'olinmadi' }}"
                                data-para3="{{ $data['paralar'][3]['olindi'] ? 'olindi' : 'olinmadi' }}"
                                x-show="filterGuruh($el)">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-900">{{ $data['guruh']->nomi }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $data['guruh']->kurs == 1 ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $data['guruh']->kurs }}-kurs
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $data['guruh']->talabalar->count() }}
                                </td>
                                @foreach ([1, 2, 3] as $para)
                                    <td class="px-4 py-3 text-center">
                                        @php $paraInfo = $data['paralar'][$para]; @endphp
                                        @if($paraInfo['olindi'])
                                            <div class="inline-flex flex-col items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                    {{ $paraInfo['kelganlar'] }}/{{ $paraInfo['jami'] }}
                                                </span>
                                                @if($paraInfo['olgan_odam'])
                                                    <span class="text-xs text-gray-400 mt-1">{{ $paraInfo['olgan_odam']->name }}</span>
                                                @endif
                                            </div>
                                        @elseif($paraInfo['tugadi'])
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                Olinmadi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                                Kutilmoqda
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Davomat oluvchilar tab --}}
        <div x-show="activeTab === 'oluvchilar'" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($davomatOluvchilar as $oluvchi)
                    @php
                        $oluvchiDavomatlari = \App\Models\Davomat::where('xodim_id', $oluvchi->id)
                            ->where('sana', $sana)
                            ->get();

                        // Para_1, para_2, para_3 ustunlaridan guruhlarni sanash
                        $para1Guruhlar = $oluvchiDavomatlari->filter(fn($d) => $d->para_1 !== null)->unique('guruh_id');
                        $para2Guruhlar = $oluvchiDavomatlari->filter(fn($d) => $d->para_2 !== null)->unique('guruh_id');
                        $para3Guruhlar = $oluvchiDavomatlari->filter(fn($d) => $d->para_3 !== null)->unique('guruh_id');

                        $para1Count = $para1Guruhlar->count();
                        $para2Count = $para2Guruhlar->count();
                        $para3Count = $para3Guruhlar->count();
                        $jamiGuruhlar = $oluvchiDavomatlari->unique('guruh_id')->count();
                    @endphp
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-600 font-bold">{{ substr($oluvchi->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $oluvchi->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $oluvchi->email }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">1-para:</span>
                                <span class="font-medium {{ $para1Count > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $para1Count }} guruh
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">2-para:</span>
                                <span class="font-medium {{ $para2Count > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $para2Count }} guruh
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">3-para:</span>
                                <span class="font-medium {{ $para3Count > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $para3Count }} guruh
                                </span>
                            </div>
                            <hr class="my-2">
                            <div class="flex items-center justify-between text-sm font-medium">
                                <span class="text-gray-700">Jami:</span>
                                <span class="text-indigo-600">{{ $jamiGuruhlar }} guruh</span>
                            </div>
                        </div>

                        <a href="{{ route('davomat.hisobot.oluvchi', ['user' => $oluvchi->id]) }}"
                            class="mt-3 w-full inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                            Batafsil ko'rish
                        </a>
                    </div>
                @endforeach
            </div>

            @if($davomatOluvchilar->isEmpty())
                <div class="text-center py-10 text-gray-500">
                    <i data-lucide="user-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p>Faol davomat oluvchilar yo'q</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function hisobotApp() {
    return {
        activeTab: 'guruhlar',
        qidiruv: '',
        kursFilter: '',
        paraFilter: '',
        holatFilter: '',
        hozirgiVaqt: '{{ $hozirgiVaqt }}',

        init() {
            // Vaqtni yangilash
            setInterval(() => {
                const now = new Date();
                this.hozirgiVaqt = now.toLocaleTimeString('uz-UZ', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }, 1000);

            // Lucide iconlarni qayta yuklash
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        },

        filterGuruh(el) {
            const guruhNomi = el.dataset.guruh;
            const kurs = el.dataset.kurs;
            const para1 = el.dataset.para1;
            const para2 = el.dataset.para2;
            const para3 = el.dataset.para3;

            // Qidiruv filtri
            if (this.qidiruv && !guruhNomi.includes(this.qidiruv.toLowerCase())) {
                return false;
            }

            // Kurs filtri
            if (this.kursFilter && kurs !== this.kursFilter) {
                return false;
            }

            // Para filtri
            if (this.paraFilter) {
                const paraHolat = el.dataset[`para${this.paraFilter}`];
                // Faqat tanlangan parada davomat olinganlarni ko'rsatish
            }

            // Holat filtri
            if (this.holatFilter) {
                if (this.holatFilter === 'olindi') {
                    // Kamida bitta parada davomat olingan bo'lishi kerak
                    if (para1 !== 'olindi' && para2 !== 'olindi' && para3 !== 'olindi') {
                        return false;
                    }
                } else if (this.holatFilter === 'olinmadi') {
                    // Kamida bitta parada davomat olinmagan bo'lishi kerak
                    if (para1 === 'olindi' && para2 === 'olindi' && para3 === 'olindi') {
                        return false;
                    }
                }
            }

            return true;
        }
    }
}
</script>
@endsection
