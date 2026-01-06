<?php

namespace App\Http\Controllers;

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

        // Jami statistika
        $jamiGuruhlar = Guruh::aktiv()->count();
        $jamiTalabalar = Talaba::aktiv()->count();

        return view('dashboard', compact(
            'bugungiStatistika',
            'haftalikOrtacha',
            'guruhlarStatistikasi',
            'engKopYoqTalabalar',
            'kunlikTrend',
            'jamiGuruhlar',
            'jamiTalabalar'
        ));
    }

    /**
     * Bugungi davomat statistikasi
     */
    private function getBugungiStatistika(): array
    {
        $bugun = now()->toDateString();

        // Aktiv talabalar soni
        $jamiTalabalar = Talaba::aktiv()->count();

        // Bugungi davomat olingan talabalar
        $davomatlar = Davomat::where('sana', $bugun)->get();

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
     * Guruhlar bo'yicha statistika
     */
    private function getGuruhlarStatistikasi(): array
    {
        $bugun = now()->toDateString();

        $guruhlar = Guruh::aktiv()
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
