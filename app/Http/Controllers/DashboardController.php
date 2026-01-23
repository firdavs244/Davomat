<?php

namespace App\Http\Controllers;

use App\Helpers\ParaVaqtlari;
use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard sahifasi
     */
    public function index()
    {
        $bugun = ParaVaqtlari::bugungiSana();
        $paraHolati = ParaVaqtlari::holatInfo();

        // Umumiy statistika
        $jamiGuruhlar = Guruh::aktiv()->whereIn('kurs', [1, 2])->count();
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        // Bugungi para bo'yicha statistika
        $bugungiParaStatistikasi = $this->getBugungiParaStatistikasi();

        // Bugungi umumiy statistika
        $bugungiUmumiy = $this->getBugungiUmumiyStatistika();

        // Haftalik statistika
        $haftalikStatistika = $this->getHaftalikStatistika();

        // Oylik statistika
        $oylikStatistika = $this->getOylikStatistika();

        // Kurslar bo'yicha bugungi statistika
        $kurslarStatistikasi = $this->getKurslarStatistikasi();

        // Guruhlar statistikasi
        $guruhlarStatistikasi = $this->getGuruhlarStatistikasi();

        // Top yo'qliklar
        $topYoqlar = $this->getTopYoqlar();

        // 7 kunlik trend
        $kunlikTrend = $this->getKunlikTrend();

        return view('dashboard', compact(
            'paraHolati',
            'jamiGuruhlar',
            'jamiTalabalar',
            'bugungiParaStatistikasi',
            'bugungiUmumiy',
            'haftalikStatistika',
            'oylikStatistika',
            'kurslarStatistikasi',
            'guruhlarStatistikasi',
            'topYoqlar',
            'kunlikTrend'
        ));
    }

    /**
     * Bugungi har bir para uchun statistika
     */
    private function getBugungiParaStatistikasi(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();
        $paralar = ParaVaqtlari::paralar();
        $statistika = [];

        // Jami guruhlar va talabalar soni
        $jamiGuruhlar = Guruh::aktiv()->whereIn('kurs', [1, 2])->count();
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        foreach ([1, 2, 3, 4] as $para) {
            $paraField = 'para_' . $para;
            $paraTugadi = ParaVaqtlari::paraTugadimi($para);
            $hozirgiPara = ParaVaqtlari::hozirgiPara();

            // Bu para uchun davomat oligan guruhlar
            $davomatOlinganGuruhlar = Davomat::where('sana', $bugun)
                ->whereNotNull($paraField)
                ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
                ->distinct('guruh_id')
                ->count('guruh_id');

            // Davomat statistikasi
            $davomatlar = Davomat::where('sana', $bugun)
                ->whereNotNull($paraField)
                ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
                ->get();

            $bor = $davomatlar->where($paraField, 'bor')->count();
            $yoq = $davomatlar->where($paraField, 'yoq')->count();
            $davomatOlingan = $bor + $yoq;
            $davomatOlinmagan = $jamiTalabalar - $davomatOlingan;

            // Foizlar (jami talabalar soniga nisbatan)
            $borFoiz = $jamiTalabalar > 0 ? round(($bor / $jamiTalabalar) * 100, 1) : 0;
            $yoqFoiz = $jamiTalabalar > 0 ? round(($yoq / $jamiTalabalar) * 100, 1) : 0;
            $olinmaganFoiz = $jamiTalabalar > 0 ? round(($davomatOlinmagan / $jamiTalabalar) * 100, 1) : 0;
            $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

            // Kurs bo'yicha statistika
            $kurs1 = $this->getParaKursStatistika($bugun, $para, 1);
            $kurs2 = $this->getParaKursStatistika($bugun, $para, 2);

            $statistika[$para] = [
                'para' => $para,
                'vaqt' => $paralar[$para],
                'holat' => $this->getParaHolati($para, $paraTugadi, $hozirgiPara),
                'tugadi' => $paraTugadi,
                'hozirgi' => $hozirgiPara === $para,
                'jami_guruhlar' => $jamiGuruhlar,
                'jami_talabalar' => $jamiTalabalar,
                'davomat_olingan_guruhlar' => $davomatOlinganGuruhlar,
                'olinmagan_guruhlar' => $jamiGuruhlar - $davomatOlinganGuruhlar,
                'bor' => $bor,
                'bor_foiz' => $borFoiz,
                'yoq' => $yoq,
                'yoq_foiz' => $yoqFoiz,
                'davomat_olingan' => $davomatOlingan,
                'davomat_olinmagan' => $davomatOlinmagan,
                'olinmagan_foiz' => $olinmaganFoiz,
                'davomat_foiz' => $davomatFoiz,
                'kurs1' => $kurs1,
                'kurs2' => $kurs2,
            ];
        }

        return $statistika;
    }

    /**
     * Para kurs statistikasi
     */
    private function getParaKursStatistika(string $bugun, int $para, int $kurs): array
    {
        $paraField = 'para_' . $para;

        // Kurs bo'yicha jami talabalar
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
            ->count();

        $davomatlar = Davomat::where('sana', $bugun)
            ->whereNotNull($paraField)
            ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
            ->get();

        $bor = $davomatlar->where($paraField, 'bor')->count();
        $yoq = $davomatlar->where($paraField, 'yoq')->count();
        $davomatOlingan = $bor + $yoq;
        $davomatOlinmagan = $jamiTalabalar - $davomatOlingan;

        // Foizlar
        $borFoiz = $jamiTalabalar > 0 ? round(($bor / $jamiTalabalar) * 100, 1) : 0;
        $yoqFoiz = $jamiTalabalar > 0 ? round(($yoq / $jamiTalabalar) * 100, 1) : 0;
        $olinmaganFoiz = $jamiTalabalar > 0 ? round(($davomatOlinmagan / $jamiTalabalar) * 100, 1) : 0;
        $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

        return [
            'jami_talabalar' => $jamiTalabalar,
            'bor' => $bor,
            'bor_foiz' => $borFoiz,
            'yoq' => $yoq,
            'yoq_foiz' => $yoqFoiz,
            'davomat_olingan' => $davomatOlingan,
            'davomat_olinmagan' => $davomatOlinmagan,
            'olinmagan_foiz' => $olinmaganFoiz,
            'davomat_foiz' => $davomatFoiz,
        ];
    }

    /**
     * Para holati
     */
    private function getParaHolati(int $para, bool $tugadi, ?int $hozirgiPara): string
    {
        if ($hozirgiPara === $para) {
            return 'davom';
        }
        if ($tugadi) {
            return 'tugagan';
        }
        return 'kutilmoqda';
    }

    /**
     * Bugungi umumiy statistika
     */
    private function getBugungiUmumiyStatistika(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();

        // Jami talabalar soni (1-2 kurs)
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        // Kutilayotgan jami davomat (talabalar * 4 para)
        $jamiKutilayotgan = $jamiTalabalar * 4;

        $davomatlar = Davomat::where('sana', $bugun)
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->get();

        $bor = 0;
        $yoq = 0;
        $davomatOlingan = 0;

        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                if ($d->$para !== null) {
                    $davomatOlingan++;
                    if ($d->$para === 'bor') $bor++;
                    elseif ($d->$para === 'yoq') $yoq++;
                }
            }
        }

        $davomatOlinmagan = $jamiKutilayotgan - $davomatOlingan;

        // Foizlar (jami kutilayotganga nisbatan)
        $borFoiz = $jamiKutilayotgan > 0 ? round(($bor / $jamiKutilayotgan) * 100, 1) : 0;
        $yoqFoiz = $jamiKutilayotgan > 0 ? round(($yoq / $jamiKutilayotgan) * 100, 1) : 0;
        $olinmaganFoiz = $jamiKutilayotgan > 0 ? round(($davomatOlinmagan / $jamiKutilayotgan) * 100, 1) : 0;
        $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

        return [
            'jami_talabalar' => $jamiTalabalar,
            'jami_kutilayotgan' => $jamiKutilayotgan,
            'bor' => $bor,
            'bor_foiz' => $borFoiz,
            'yoq' => $yoq,
            'yoq_foiz' => $yoqFoiz,
            'davomat_olingan' => $davomatOlingan,
            'davomat_olinmagan' => $davomatOlinmagan,
            'olinmagan_foiz' => $olinmaganFoiz,
            'davomat_foiz' => $davomatFoiz,
            'talabalar_keldi' => $davomatlar->unique('talaba_id')->count(),
        ];
    }

    /**
     * Haftalik statistika
     */
    private function getHaftalikStatistika(): array
    {
        $haftaBoshi = Carbon::now()->startOfWeek();
        $bugun = Carbon::now();
        $kunlar = $haftaBoshi->diffInDays($bugun) + 1;

        // Jami talabalar soni
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        // Kutilayotgan jami davomat (talabalar * 4 para * kunlar)
        $jamiKutilayotgan = $jamiTalabalar * 4 * $kunlar;

        $davomatlar = Davomat::whereBetween('sana', [$haftaBoshi->toDateString(), $bugun->toDateString()])
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->get();

        $bor = 0;
        $yoq = 0;
        $davomatOlingan = 0;

        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                if ($d->$para !== null) {
                    $davomatOlingan++;
                    if ($d->$para === 'bor') $bor++;
                    elseif ($d->$para === 'yoq') $yoq++;
                }
            }
        }

        $davomatOlinmagan = $jamiKutilayotgan - $davomatOlingan;

        // Foizlar
        $borFoiz = $jamiKutilayotgan > 0 ? round(($bor / $jamiKutilayotgan) * 100, 1) : 0;
        $yoqFoiz = $jamiKutilayotgan > 0 ? round(($yoq / $jamiKutilayotgan) * 100, 1) : 0;
        $olinmaganFoiz = $jamiKutilayotgan > 0 ? round(($davomatOlinmagan / $jamiKutilayotgan) * 100, 1) : 0;
        $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

        // Kurslar bo'yicha
        $kurs1 = $this->getKursStatistikaByPeriod($haftaBoshi, $bugun, 1, $kunlar);
        $kurs2 = $this->getKursStatistikaByPeriod($haftaBoshi, $bugun, 2, $kunlar);

        return [
            'jami_talabalar' => $jamiTalabalar,
            'jami_kutilayotgan' => $jamiKutilayotgan,
            'bor' => $bor,
            'bor_foiz' => $borFoiz,
            'yoq' => $yoq,
            'yoq_foiz' => $yoqFoiz,
            'davomat_olingan' => $davomatOlingan,
            'davomat_olinmagan' => $davomatOlinmagan,
            'olinmagan_foiz' => $olinmaganFoiz,
            'davomat_foiz' => $davomatFoiz,
            'kunlar' => $kunlar,
            'boshlanish' => $haftaBoshi->format('d.m'),
            'tugash' => $bugun->format('d.m'),
            'kurs1' => $kurs1,
            'kurs2' => $kurs2,
        ];
    }

    /**
     * Oylik statistika
     */
    private function getOylikStatistika(): array
    {
        $oyBoshi = Carbon::now()->startOfMonth();
        $bugun = Carbon::now();
        $kunlar = $oyBoshi->diffInDays($bugun) + 1;

        // Jami talabalar soni
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        // Kutilayotgan jami davomat (talabalar * 4 para * kunlar)
        $jamiKutilayotgan = $jamiTalabalar * 4 * $kunlar;

        $davomatlar = Davomat::whereBetween('sana', [$oyBoshi->toDateString(), $bugun->toDateString()])
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->get();

        $bor = 0;
        $yoq = 0;
        $davomatOlingan = 0;

        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                if ($d->$para !== null) {
                    $davomatOlingan++;
                    if ($d->$para === 'bor') $bor++;
                    elseif ($d->$para === 'yoq') $yoq++;
                }
            }
        }

        $davomatOlinmagan = $jamiKutilayotgan - $davomatOlingan;

        // Foizlar
        $borFoiz = $jamiKutilayotgan > 0 ? round(($bor / $jamiKutilayotgan) * 100, 1) : 0;
        $yoqFoiz = $jamiKutilayotgan > 0 ? round(($yoq / $jamiKutilayotgan) * 100, 1) : 0;
        $olinmaganFoiz = $jamiKutilayotgan > 0 ? round(($davomatOlinmagan / $jamiKutilayotgan) * 100, 1) : 0;
        $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

        // Kurslar bo'yicha
        $kurs1 = $this->getKursStatistikaByPeriod($oyBoshi, $bugun, 1, $kunlar);
        $kurs2 = $this->getKursStatistikaByPeriod($oyBoshi, $bugun, 2, $kunlar);

        return [
            'jami_talabalar' => $jamiTalabalar,
            'jami_kutilayotgan' => $jamiKutilayotgan,
            'bor' => $bor,
            'bor_foiz' => $borFoiz,
            'yoq' => $yoq,
            'yoq_foiz' => $yoqFoiz,
            'davomat_olingan' => $davomatOlingan,
            'davomat_olinmagan' => $davomatOlinmagan,
            'olinmagan_foiz' => $olinmaganFoiz,
            'davomat_foiz' => $davomatFoiz,
            'kunlar' => $kunlar,
            'oy_nomi' => $this->getOyNomi($bugun->month),
            'kurs1' => $kurs1,
            'kurs2' => $kurs2,
        ];
    }

    /**
     * Kurslar bo'yicha bugungi statistika
     */
    private function getKurslarStatistikasi(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();
        $statistika = [];

        foreach ([1, 2] as $kurs) {
            $jamiTalabalar = Talaba::aktiv()
                ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
                ->count();

            $jamiGuruhlar = Guruh::aktiv()->where('kurs', $kurs)->count();

            // Kutilayotgan jami davomat (talabalar * 4 para)
            $jamiKutilayotgan = $jamiTalabalar * 4;

            $davomatlar = Davomat::where('sana', $bugun)
                ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
                ->get();

            $bor = 0;
            $yoq = 0;
            $davomatOlingan = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                    if ($d->$para !== null) {
                        $davomatOlingan++;
                        if ($d->$para === 'bor') $bor++;
                        elseif ($d->$para === 'yoq') $yoq++;
                    }
                }
            }

            $davomatOlinmagan = $jamiKutilayotgan - $davomatOlingan;

            // Foizlar
            $borFoiz = $jamiKutilayotgan > 0 ? round(($bor / $jamiKutilayotgan) * 100, 1) : 0;
            $yoqFoiz = $jamiKutilayotgan > 0 ? round(($yoq / $jamiKutilayotgan) * 100, 1) : 0;
            $olinmaganFoiz = $jamiKutilayotgan > 0 ? round(($davomatOlinmagan / $jamiKutilayotgan) * 100, 1) : 0;
            $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

            $statistika[$kurs] = [
                'kurs' => $kurs,
                'jami_talabalar' => $jamiTalabalar,
                'jami_guruhlar' => $jamiGuruhlar,
                'jami_kutilayotgan' => $jamiKutilayotgan,
                'bor' => $bor,
                'bor_foiz' => $borFoiz,
                'yoq' => $yoq,
                'yoq_foiz' => $yoqFoiz,
                'davomat_olingan' => $davomatOlingan,
                'davomat_olinmagan' => $davomatOlinmagan,
                'olinmagan_foiz' => $olinmaganFoiz,
                'davomat_foiz' => $davomatFoiz,
            ];
        }

        return $statistika;
    }

    /**
     * Davr bo'yicha kurs statistikasi (haftalik/oylik uchun)
     */
    private function getKursStatistikaByPeriod(Carbon $boshlanish, Carbon $tugash, int $kurs, int $kunlar): array
    {
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
            ->count();

        $jamiKutilayotgan = $jamiTalabalar * 4 * $kunlar;

        $davomatlar = Davomat::whereBetween('sana', [$boshlanish->toDateString(), $tugash->toDateString()])
            ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
            ->get();

        $bor = 0;
        $yoq = 0;
        $davomatOlingan = 0;

        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                if ($d->$para !== null) {
                    $davomatOlingan++;
                    if ($d->$para === 'bor') $bor++;
                    elseif ($d->$para === 'yoq') $yoq++;
                }
            }
        }

        $davomatOlinmagan = $jamiKutilayotgan - $davomatOlingan;

        $borFoiz = $jamiKutilayotgan > 0 ? round(($bor / $jamiKutilayotgan) * 100, 1) : 0;
        $yoqFoiz = $jamiKutilayotgan > 0 ? round(($yoq / $jamiKutilayotgan) * 100, 1) : 0;
        $olinmaganFoiz = $jamiKutilayotgan > 0 ? round(($davomatOlinmagan / $jamiKutilayotgan) * 100, 1) : 0;
        $davomatFoiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

        return [
            'jami_talabalar' => $jamiTalabalar,
            'jami_kutilayotgan' => $jamiKutilayotgan,
            'bor' => $bor,
            'bor_foiz' => $borFoiz,
            'yoq' => $yoq,
            'yoq_foiz' => $yoqFoiz,
            'davomat_olingan' => $davomatOlingan,
            'davomat_olinmagan' => $davomatOlinmagan,
            'olinmagan_foiz' => $olinmaganFoiz,
            'davomat_foiz' => $davomatFoiz,
        ];
    }

    /**
     * Guruhlar statistikasi
     */
    private function getGuruhlarStatistikasi(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();

        $guruhlar = Guruh::aktiv()
            ->whereIn('kurs', [1, 2])
            ->withCount(['aktivTalabalar'])
            ->get();

        $statistika = [];

        foreach ($guruhlar as $guruh) {
            $davomatlar = Davomat::where('guruh_id', $guruh->id)
                ->where('sana', $bugun)
                ->get();

            $bor = 0;
            $yoq = 0;
            $jami = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                    if ($d->$para !== null) {
                        $jami++;
                        if ($d->$para === 'bor') $bor++;
                        elseif ($d->$para === 'yoq') $yoq++;
                    }
                }
            }

            $foiz = $jami > 0 ? round(($bor / $jami) * 100, 1) : 0;

            $statistika[] = [
                'guruh' => $guruh,
                'talabalar_soni' => $guruh->aktiv_talabalar_count,
                'bor' => $bor,
                'yoq' => $yoq,
                'foiz' => $foiz,
            ];
        }

        usort($statistika, fn($a, $b) => $b['foiz'] <=> $a['foiz']);

        return $statistika;
    }

    /**
     * Top yo'qliklar (bu oy)
     */
    private function getTopYoqlar(int $limit = 10): array
    {
        $oyBoshi = Carbon::now()->startOfMonth();
        $bugun = Carbon::now();

        $talabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->with('guruh')
            ->get();

        $statistika = [];

        foreach ($talabalar as $talaba) {
            $davomatlar = $talaba->davomatlar()
                ->whereBetween('sana', [$oyBoshi, $bugun])
                ->get();

            $yoqSoni = 0;
            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                    if ($d->$para === 'yoq') $yoqSoni++;
                }
            }

            if ($yoqSoni > 0) {
                $statistika[] = [
                    'talaba' => $talaba,
                    'yoq_soni' => $yoqSoni,
                ];
            }
        }

        usort($statistika, fn($a, $b) => $b['yoq_soni'] <=> $a['yoq_soni']);

        return array_slice($statistika, 0, $limit);
    }

    /**
     * 7 kunlik trend
     */
    private function getKunlikTrend(): array
    {
        $trend = [];

        for ($i = 6; $i >= 0; $i--) {
            $sana = Carbon::now()->subDays($i);
            $sanaStr = $sana->toDateString();

            $davomatlar = Davomat::where('sana', $sanaStr)
                ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
                ->get();

            $bor = 0;
            $yoq = 0;
            $jami = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3', 'para_4'] as $para) {
                    if ($d->$para !== null) {
                        $jami++;
                        if ($d->$para === 'bor') $bor++;
                        elseif ($d->$para === 'yoq') $yoq++;
                    }
                }
            }

            $foiz = $jami > 0 ? round(($bor / $jami) * 100, 1) : 0;

            $trend[] = [
                'sana' => $sana->format('d.m'),
                'kun' => $this->getKunNomi($sana->dayOfWeek),
                'foiz' => $foiz,
                'bor' => $bor,
                'yoq' => $yoq,
            ];
        }

        return $trend;
    }

    /**
     * Kun nomi
     */
    private function getKunNomi(int $dayOfWeek): string
    {
        return ['Yak', 'Dush', 'Sesh', 'Chor', 'Pay', 'Jum', 'Shan'][$dayOfWeek] ?? '';
    }

    /**
     * Oy nomi
     */
    private function getOyNomi(int $month): string
    {
        return ['', 'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentabr', 'Oktabr', 'Noyabr', 'Dekabr'][$month] ?? '';
    }

    /**
     * Real-time statistika (AJAX)
     */
    public function getRealTimeStats()
    {
        return response()->json([
            'para_holati' => ParaVaqtlari::holatInfo(),
            'bugungi_umumiy' => $this->getBugungiUmumiyStatistika(),
            'bugungi_paralar' => $this->getBugungiParaStatistikasi(),
            'kurslar' => $this->getKurslarStatistikasi(),
            'server_vaqt' => ParaVaqtlari::hozirgiVaqt()->format('H:i:s'),
        ]);
    }

    /**
     * Statistikani yangilash (AJAX)
     */
    public function refreshStats()
    {
        return response()->json([
            'para_holati' => ParaVaqtlari::holatInfo(),
            'bugungi' => $this->getBugungiUmumiyStatistika(),
        ]);
    }
}
