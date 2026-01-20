<?php

namespace App\Http\Controllers;

use App\Helpers\ParaVaqtlari;
use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard sahifasi
     */
    public function index()
    {
        $user = auth()->user();

        // Bugungi statistika
        $bugungiStatistika = $this->getBugungiStatistika();

        // Haftalik o'rtacha
        $haftalikOrtacha = Davomat::haftalikOrtacha();

        // Guruhlar bo'yicha statistika
        $guruhlarStatistikasi = $this->getGuruhlarStatistikasi();

        // Eng ko'p yo'q bo'lgan talabalar (top 10)
        $engKopYoqTalabalar = $this->getEngKopYoqTalabalar();

        // Oxirgi 7 kunlik trend
        $kunlikTrend = $this->getKunlikTrend();

        // Jami statistika (faqat 1 va 2-kurs)
        $jamiGuruhlar = Guruh::aktiv()->whereIn('kurs', [1, 2])->count();
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        // Kurs bo'yicha hozirgi para statistikasi (real-time)
        $kursStatistikasi = $this->getKursStatistikasi();

        // Para holati
        $paraHolati = ParaVaqtlari::holatInfo();

        // Tugagan paralar umumiy ma'lumoti
        $tugaganParalarSummary = $this->getTugaganParalarSummary();

        return view('dashboard', compact(
            'bugungiStatistika',
            'haftalikOrtacha',
            'guruhlarStatistikasi',
            'engKopYoqTalabalar',
            'kunlikTrend',
            'jamiGuruhlar',
            'jamiTalabalar',
            'kursStatistikasi',
            'paraHolati',
            'tugaganParalarSummary'
        ));
    }

    /**
     * Bugungi davomat statistikasi
     */
    private function getBugungiStatistika(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();

        // Aktiv talabalar soni (faqat 1 va 2-kurs)
        $jamiTalabalar = Talaba::aktiv()
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->count();

        // Bugungi davomat olingan talabalar
        $davomatlar = Davomat::where('sana', $bugun)
            ->whereHas('guruh', fn($q) => $q->whereIn('kurs', [1, 2]))
            ->get();

        $borParalar = 0;
        $yoqParalar = 0;
        $jamiParalar = 0;

        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para !== null) {
                    $jamiParalar++;
                    if ($d->$para === 'bor') $borParalar++;
                    elseif ($d->$para === 'yoq') $yoqParalar++;
                }
            }
        }

        $foiz = $jamiParalar > 0 ? round(($borParalar / $jamiParalar) * 100, 1) : 0;

        return [
            'jami_talabalar' => $jamiTalabalar,
            'davomat_olingan' => $davomatlar->count(),
            'bor' => $borParalar,
            'yoq' => $yoqParalar,
            'foiz' => $foiz,
        ];
    }

    /**
     * Kurs bo'yicha hozirgi para statistikasi (real-time)
     */
    private function getKursStatistikasi(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();
        $hozirgiPara = ParaVaqtlari::hozirgiDavomatPara();

        $statistika = [];

        foreach ([1, 2] as $kurs) {
            // Kurs bo'yicha jami aktiv talabalar
            $jamiTalabalar = Talaba::aktiv()
                ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
                ->count();

            // Bugungi hozirgi para uchun davomat
            $paraField = $hozirgiPara ? 'para_' . $hozirgiPara : null;

            $bor = 0;
            $yoq = 0;
            $davomatOlingan = 0;

            if ($paraField) {
                $davomatlar = Davomat::where('sana', $bugun)
                    ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
                    ->whereNotNull($paraField)
                    ->get();

                $davomatOlingan = $davomatlar->count();

                foreach ($davomatlar as $d) {
                    if ($d->$paraField === 'bor') $bor++;
                    elseif ($d->$paraField === 'yoq') $yoq++;
                }
            }

            $foiz = $davomatOlingan > 0 ? round(($bor / $davomatOlingan) * 100, 1) : 0;

            $statistika[$kurs] = [
                'kurs' => $kurs,
                'jami_talabalar' => $jamiTalabalar,
                'davomat_olingan' => $davomatOlingan,
                'bor' => $bor,
                'yoq' => $yoq,
                'foiz' => $foiz,
                'guruhlar_soni' => Guruh::aktiv()->where('kurs', $kurs)->count(),
            ];
        }

        return $statistika;
    }

    /**
     * Guruhlar bo'yicha statistika
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
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
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

        // Foiz bo'yicha saralash (eng yaxshidan eng yomonga)
        usort($statistika, fn($a, $b) => $b['foiz'] <=> $a['foiz']);

        return $statistika;
    }

    /**
     * Eng ko'p yo'q bo'lgan talabalar
     */
    private function getEngKopYoqTalabalar(int $limit = 10): array
    {
        $oyBoshi = now()->startOfMonth();
        $bugun = now();

        $talabalar = Talaba::aktiv()
            ->with('guruh')
            ->get();

        $statistika = [];

        foreach ($talabalar as $talaba) {
            $davomatlar = $talaba->davomatlar()
                ->whereBetween('sana', [$oyBoshi, $bugun])
                ->get();

            $yoqSoni = 0;
            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
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

        // Yo'qlik soni bo'yicha saralash
        usort($statistika, fn($a, $b) => $b['yoq_soni'] <=> $a['yoq_soni']);

        return array_slice($statistika, 0, $limit);
    }

    /**
     * Tugagan paralar uchun umumiy summary
     * Bu funksiya dashboard'da tugagan paralar haqida ma'lumot ko'rsatish uchun
     */
    private function getTugaganParalarSummary(): array
    {
        $bugun = ParaVaqtlari::bugungiSana();
        $paralar = ParaVaqtlari::paralar();
        $summary = [];

        // Bugungi barcha davomatlar
        $barDavomatlar = Davomat::where('sana', $bugun)
            ->with(['guruh'])
            ->get();

        foreach ([1, 2, 3] as $para) {
            // Para tugaganmi tekshirish
            $paraTugadi = ParaVaqtlari::paraTugadimi($para);
            $paraField = 'para_' . $para;

            if (!$paraTugadi) {
                $summary[$para] = [
                    'tugadi' => false,
                    'vaqt' => $paralar[$para],
                ];
                continue;
            }

            // Bu para uchun to'ldirilgan davomatlar
            $davomatlar = $barDavomatlar->filter(fn($d) => $d->$paraField !== null);

            // Faqat 1-kurs va 2-kurs guruhlar
            $jamiGuruhlar = Guruh::aktiv()->whereIn('kurs', [1, 2])->count();
            $davomatOlinganGuruhlar = $davomatlar->unique('guruh_id')->count();

            // Kelgan/Kelmaganlar soni
            $keldi = $davomatlar->where($paraField, 'bor')->count();
            $kelmadi = $davomatlar->where($paraField, 'yoq')->count();
            $jami = $keldi + $kelmadi;

            // Kurs bo'yicha statistika
            $kurs1Keldi = 0;
            $kurs1Jami = 0;
            $kurs2Keldi = 0;
            $kurs2Jami = 0;

            foreach ($davomatlar as $d) {
                if ($d->guruh && $d->guruh->kurs == 1) {
                    $kurs1Jami++;
                    if ($d->$paraField == 'bor') $kurs1Keldi++;
                } elseif ($d->guruh && $d->guruh->kurs == 2) {
                    $kurs2Jami++;
                    if ($d->$paraField == 'bor') $kurs2Keldi++;
                }
            }

            $summary[$para] = [
                'tugadi' => true,
                'vaqt' => $paralar[$para],
                'jami_guruhlar' => $jamiGuruhlar,
                'davomat_olingan_guruhlar' => $davomatOlinganGuruhlar,
                'olinmagan_guruhlar' => $jamiGuruhlar - $davomatOlinganGuruhlar,
                'jami_talabalar' => $jami,
                'keldi' => $keldi,
                'kelmadi' => $kelmadi,
                'foiz' => $jami > 0 ? round(($keldi / $jami) * 100, 1) : 0,
                'kurs1' => [
                    'keldi' => $kurs1Keldi,
                    'jami' => $kurs1Jami,
                    'foiz' => $kurs1Jami > 0 ? round(($kurs1Keldi / $kurs1Jami) * 100, 1) : 0,
                ],
                'kurs2' => [
                    'keldi' => $kurs2Keldi,
                    'jami' => $kurs2Jami,
                    'foiz' => $kurs2Jami > 0 ? round(($kurs2Keldi / $kurs2Jami) * 100, 1) : 0,
                ],
            ];
        }

        return $summary;
    }

    /**
     * Oxirgi 7 kunlik davomat trendi
     */
    private function getKunlikTrend(): array
    {
        $trend = [];

        for ($i = 6; $i >= 0; $i--) {
            $sana = now()->subDays($i);
            $sanaStr = $sana->toDateString();

            $davomatlar = Davomat::where('sana', $sanaStr)->get();

            $bor = 0;
            $jami = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
                    if ($d->$para !== null) {
                        $jami++;
                        if ($d->$para === 'bor') $bor++;
                    }
                }
            }

            $foiz = $jami > 0 ? round(($bor / $jami) * 100, 1) : 0;

            $trend[] = [
                'sana' => $sana->format('d.m'),
                'kun' => $this->getKunNomi($sana->dayOfWeek),
                'foiz' => $foiz,
                'bor' => $bor,
                'jami' => $jami,
            ];
        }

        return $trend;
    }

    /**
     * Kun nomini olish
     */
    private function getKunNomi(int $dayOfWeek): string
    {
        $kunlar = [
            0 => 'Yak',
            1 => 'Dush',
            2 => 'Sesh',
            3 => 'Chor',
            4 => 'Pay',
            5 => 'Jum',
            6 => 'Shan',
        ];

        return $kunlar[$dayOfWeek] ?? '';
    }

    /**
     * AJAX orqali yangilangan statistika
     */
    public function refreshStats()
    {
        return response()->json([
            'bugungi' => $this->getBugungiStatistika(),
            'haftalik_ortacha' => Davomat::haftalikOrtacha(),
            'kurs_statistikasi' => $this->getKursStatistikasi(),
            'para_holati' => ParaVaqtlari::holatInfo(),
        ]);
    }

    /**
     * AJAX: Kurs statistikasi (real-time)
     */
    public function getKursStatistika()
    {
        return response()->json([
            'kurs_statistikasi' => $this->getKursStatistikasi(),
            'para_holati' => ParaVaqtlari::holatInfo(),
            'bugungi' => $this->getBugungiStatistika(),
        ]);
    }

    /**
     * AJAX: Real-time statistika (har 30 sekundda yangilanadi)
     */
    public function getRealTimeStats()
    {
        $hozirgiPara = ParaVaqtlari::hozirgiDavomatPara();
        $bugun = ParaVaqtlari::bugungiSana();

        // Kurslar bo'yicha hozirgi para statistikasi
        $kurslar = [];

        foreach ([1, 2] as $kurs) {
            $jamiTalabalar = Talaba::aktiv()
                ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
                ->count();

            $jamiGuruhlar = Guruh::aktiv()->where('kurs', $kurs)->count();

            $paraStats = [];

            // Har bir para uchun statistika
            foreach ([1, 2, 3] as $para) {
                $paraField = 'para_' . $para;

                $davomatlar = Davomat::where('sana', $bugun)
                    ->whereHas('guruh', fn($q) => $q->where('kurs', $kurs))
                    ->whereNotNull($paraField)
                    ->get();

                $bor = $davomatlar->where($paraField, 'bor')->count();
                $yoq = $davomatlar->where($paraField, 'yoq')->count();
                $jami = $bor + $yoq;

                $paraStats[$para] = [
                    'bor' => $bor,
                    'yoq' => $yoq,
                    'jami' => $jami,
                    'foiz' => $jami > 0 ? round(($bor / $jami) * 100, 1) : 0,
                    'tugagan' => ParaVaqtlari::paraTugadimi($para),
                ];
            }

            // Jami statistika (barcha paralar)
            $borJami = array_sum(array_column($paraStats, 'bor'));
            $yoqJami = array_sum(array_column($paraStats, 'yoq'));
            $jamiJami = $borJami + $yoqJami;

            $kurslar[$kurs] = [
                'kurs' => $kurs,
                'jami_talabalar' => $jamiTalabalar,
                'jami_guruhlar' => $jamiGuruhlar,
                'paralar' => $paraStats,
                'jami_bor' => $borJami,
                'jami_yoq' => $yoqJami,
                'jami_foiz' => $jamiJami > 0 ? round(($borJami / $jamiJami) * 100, 1) : 0,
            ];
        }

        return response()->json([
            'kurslar' => $kurslar,
            'para_holati' => ParaVaqtlari::holatInfo(),
            'server_vaqt' => ParaVaqtlari::hozirgiVaqt()->format('H:i:s'),
            'bugungi_sana' => $bugun,
        ]);
    }

    /**
     * Period bo'yicha statistika (AJAX)
     */
    public function getStatsByPeriod(Request $request)
    {
        $period = $request->input('period', 'daily'); // daily, weekly, monthly, yearly

        switch ($period) {
            case 'daily':
                $stats = $this->getDailyStats();
                break;
            case 'weekly':
                $stats = $this->getWeeklyStats();
                break;
            case 'monthly':
                $stats = $this->getMonthlyStats();
                break;
            case 'yearly':
                $stats = $this->getYearlyStats();
                break;
            default:
                $stats = $this->getDailyStats();
        }

        return response()->json($stats);
    }

    /**
     * Kunlik statistika
     */
    private function getDailyStats(): array
    {
        $bugun = now()->toDateString();
        $davomatlar = Davomat::where('sana', $bugun)->get();

        return $this->calculateStats($davomatlar, 'Bugun');
    }

    /**
     * Haftalik statistika
     */
    private function getWeeklyStats(): array
    {
        $haftaBoshi = now()->startOfWeek();
        $haftaOxiri = now()->endOfWeek();

        $davomatlar = Davomat::whereBetween('sana', [$haftaBoshi, $haftaOxiri])->get();

        return $this->calculateStats($davomatlar, 'Bu hafta');
    }

    /**
     * Oylik statistika
     */
    private function getMonthlyStats(): array
    {
        $oyBoshi = now()->startOfMonth();
        $oyOxiri = now()->endOfMonth();

        $davomatlar = Davomat::whereBetween('sana', [$oyBoshi, $oyOxiri])->get();

        return $this->calculateStats($davomatlar, 'Bu oy');
    }

    /**
     * Yillik statistika
     */
    private function getYearlyStats(): array
    {
        $yilBoshi = now()->startOfYear();
        $yilOxiri = now()->endOfYear();

        $davomatlar = Davomat::whereBetween('sana', [$yilBoshi, $yilOxiri])->get();

        return $this->calculateStats($davomatlar, 'Bu yil');
    }

    /**
     * Statistikani hisoblash
     */
    private function calculateStats($davomatlar, string $period): array
    {
        $bor = 0;
        $yoq = 0;
        $jami = 0;
        $davomatOlingan = $davomatlar->unique('talaba_id')->count();

        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para !== null) {
                    $jami++;
                    if ($d->$para === 'bor') $bor++;
                    elseif ($d->$para === 'yoq') $yoq++;
                }
            }
        }

        $foiz = $jami > 0 ? round(($bor / $jami) * 100, 1) : 0;

        // Guruhlar statistikasi
        $guruhlarStats = $this->getGuruhlarStatsByPeriod($period);

        // Top yo'qliklar
        $topYoqlar = $this->getTopYoqByPeriod($period);

        return [
            'period' => $period,
            'davomat_olingan' => $davomatOlingan,
            'bor' => $bor,
            'yoq' => $yoq,
            'jami' => $jami,
            'foiz' => $foiz,
            'guruhlar' => $guruhlarStats,
            'top_yoqlar' => $topYoqlar,
        ];
    }

    /**
     * Period bo'yicha guruhlar statistikasi
     */
    private function getGuruhlarStatsByPeriod(string $period): array
    {
        $dateRange = $this->getDateRangeByPeriod($period);

        $guruhlar = Guruh::aktiv()
            ->withCount(['aktivTalabalar'])
            ->get();

        $statistika = [];

        foreach ($guruhlar as $guruh) {
            $davomatlar = Davomat::where('guruh_id', $guruh->id)
                ->whereBetween('sana', $dateRange)
                ->get();

            $bor = 0;
            $yoq = 0;
            $jami = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
                    if ($d->$para !== null) {
                        $jami++;
                        if ($d->$para === 'bor') $bor++;
                        elseif ($d->$para === 'yoq') $yoq++;
                    }
                }
            }

            $foiz = $jami > 0 ? round(($bor / $jami) * 100, 1) : 0;

            if ($jami > 0) {
                $statistika[] = [
                    'guruh_id' => $guruh->id,
                    'guruh_nomi' => $guruh->nomi,
                    'talabalar_soni' => $guruh->aktiv_talabalar_count,
                    'bor' => $bor,
                    'yoq' => $yoq,
                    'foiz' => $foiz,
                ];
            }
        }

        usort($statistika, fn($a, $b) => $b['foiz'] <=> $a['foiz']);

        return $statistika;
    }

    /**
     * Period bo'yicha top yo'qliklar
     */
    private function getTopYoqByPeriod(string $period, int $limit = 10): array
    {
        $dateRange = $this->getDateRangeByPeriod($period);

        $talabalar = Talaba::aktiv()
            ->with('guruh')
            ->get();

        $statistika = [];

        foreach ($talabalar as $talaba) {
            $davomatlar = $talaba->davomatlar()
                ->whereBetween('sana', $dateRange)
                ->get();

            $yoqSoni = 0;
            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
                    if ($d->$para === 'yoq') $yoqSoni++;
                }
            }

            if ($yoqSoni > 0) {
                $statistika[] = [
                    'talaba_id' => $talaba->id,
                    'talaba_fish' => $talaba->fish,
                    'guruh_nomi' => $talaba->guruh?->nomi ?? 'Guruhsiz',
                    'yoq_soni' => $yoqSoni,
                ];
            }
        }

        usort($statistika, fn($a, $b) => $b['yoq_soni'] <=> $a['yoq_soni']);

        return array_slice($statistika, 0, $limit);
    }

    /**
     * Period bo'yicha sana oralig'ini olish
     */
    private function getDateRangeByPeriod(string $period): array
    {
        switch ($period) {
            case 'Bugun':
                return [now()->toDateString(), now()->toDateString()];
            case 'Bu hafta':
                return [now()->startOfWeek(), now()->endOfWeek()];
            case 'Bu oy':
                return [now()->startOfMonth(), now()->endOfMonth()];
            case 'Bu yil':
                return [now()->startOfYear(), now()->endOfYear()];
            default:
                return [now()->toDateString(), now()->toDateString()];
        }
    }
}
