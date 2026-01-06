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
}
