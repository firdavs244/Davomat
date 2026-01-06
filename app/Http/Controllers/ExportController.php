<?php

namespace App\Http\Controllers;

use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\Talaba;
use App\Exports\DavomatExport;
use App\Exports\GuruhHisobotiExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export sahifasi
     */
    public function index()
    {
        $guruhlar = Guruh::aktiv()->orderBy('nomi')->get();
        return view('export.index', compact('guruhlar'));
    }

    /**
     * Excel formatda export
     */
    public function exportCSV(Request $request)
    {
        $validated = $request->validate([
            'guruh_id' => 'required|exists:guruhlar,id',
            'davr' => 'required|in:kunlik,haftalik,oylik,yillik,maxsus',
            'sana_dan' => 'required_if:davr,maxsus|nullable|date',
            'sana_gacha' => 'required_if:davr,maxsus|nullable|date|after_or_equal:sana_dan',
        ], [
            'guruh_id.required' => 'Guruh tanlanishi shart',
            'davr.required' => 'Davr tanlanishi shart',
            'sana_dan.required_if' => 'Boshlanish sanasi kiritilishi shart',
            'sana_gacha.required_if' => 'Tugash sanasi kiritilishi shart',
        ]);

        $guruh = Guruh::findOrFail($validated['guruh_id']);

        // Sana oralig'ini aniqlash
        [$sanaDan, $sanaGacha] = $this->getSanaOraligi($validated['davr'], $validated);

        // Excel faylni yaratish va yuklab olish
        $fileName = "davomat_{$guruh->nomi}_{$sanaDan->format('Y-m-d')}_{$sanaGacha->format('Y-m-d')}.xlsx";

        return Excel::download(
            new DavomatExport($guruh, $sanaDan, $sanaGacha),
            $fileName
        );
    }

    /**
     * Davr bo'yicha sana oralig'ini aniqlash
     */
    private function getSanaOraligi(string $davr, array $data): array
    {
        return match ($davr) {
            'kunlik' => [now(), now()],
            'haftalik' => [now()->startOfWeek(), now()->endOfWeek()],
            'oylik' => [now()->startOfMonth(), now()->endOfMonth()],
            'yillik' => [now()->startOfYear(), now()->endOfYear()],
            'maxsus' => [Carbon::parse($data['sana_dan']), Carbon::parse($data['sana_gacha'])],
            default => [now(), now()],
        };
    }

    /**
     * Guruh bo'yicha umumiy hisobot (Excel)
     */
    public function guruhHisoboti(Request $request, Guruh $guruh)
    {
        $oy = $request->input('oy', now()->month);
        $yil = $request->input('yil', now()->year);

        // Excel formatda export qilish
        if ($request->has('export')) {
            $fileName = "guruh_hisoboti_{$guruh->nomi}_{$yil}_{$oy}.xlsx";

            return Excel::download(
                new GuruhHisobotiExport($guruh, $oy, $yil),
                $fileName
            );
        }

        // View uchun ma'lumotlar
        $oyBoshi = Carbon::create($yil, $oy, 1)->startOfMonth();
        $oyOxiri = Carbon::create($yil, $oy, 1)->endOfMonth();

        $talabalar = Talaba::where('guruh_id', $guruh->id)
            ->orderBy('fish')
            ->get();

        $hisobot = [];

        foreach ($talabalar as $talaba) {
            $davomatlar = Davomat::where('talaba_id', $talaba->id)
                ->whereBetween('sana', [$oyBoshi, $oyOxiri])
                ->get();

            $borSoni = 0;
            $yoqSoni = 0;

            foreach ($davomatlar as $d) {
                foreach (['para_1', 'para_2', 'para_3'] as $para) {
                    if ($d->$para === 'bor') $borSoni++;
                    elseif ($d->$para === 'yoq') $yoqSoni++;
                }
            }

            $jami = $borSoni + $yoqSoni;
            $foiz = $jami > 0 ? round(($borSoni / $jami) * 100, 1) : 0;

            $hisobot[] = [
                'talaba' => $talaba,
                'bor' => $borSoni,
                'yoq' => $yoqSoni,
                'foiz' => $foiz,
            ];
        }

        // Foiz bo'yicha saralash
        usort($hisobot, fn($a, $b) => $b['foiz'] <=> $a['foiz']);

        return view('export.guruh-hisoboti', compact('guruh', 'hisobot', 'oy', 'yil'));
    }
}
