<?php

namespace App\Http\Controllers;

use App\Helpers\ParaVaqtlari;
use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DavomatHisobotController extends Controller
{
    /**
     * Davomat oluvchilar hisoboti - qaysi guruhlardan davomat olindi, qaysilaridan olinmadi
     */
    public function index(Request $request)
    {
        $sana = $request->get('sana', ParaVaqtlari::bugungiSana());
        $paralar = ParaVaqtlari::paralar();
        $hozirgiVaqt = ParaVaqtlari::hozirgiVaqt();

        // Faqat 1-kurs va 2-kurs guruhlar
        $guruhlar = Guruh::whereIn('kurs', [1, 2])
            ->with(['talabalar' => function($q) {
                $q->where('holati', 'aktiv');
            }])
            ->orderBy('kurs')
            ->orderBy('nomi')
            ->get();

        // Davomat oluvchilar ro'yxati
        $davomatOluvchilar = User::where('role', 'davomat_oluvchi')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Bu sanadagi barcha davomatlar (har bir talaba uchun bitta record, para_1, para_2, para_3 ustunlari)
        $davomatlar = Davomat::where('sana', $sana)
            ->with('xodim')
            ->get()
            ->groupBy('guruh_id');

        // Guruhlar statistikasi
        $guruhlarData = [];
        foreach ($guruhlar as $guruh) {
            $paraData = [];
            $guruhDavomatlari = $davomatlar[$guruh->id] ?? collect([]);

            foreach ([1, 2, 3, 4] as $para) {
                // Para tugaganmi?
                $paraTugadi = ParaVaqtlari::paraTugadimi($para);
                $paraField = 'para_' . $para;

                // Bu para uchun davomat bormi? (kamida bitta talabada para_X to'ldirilgan)
                $paraDavomat = $guruhDavomatlari->filter(fn($d) => $d->$paraField !== null);
                $davomatOlindi = $paraDavomat->isNotEmpty();

                // Kim oldi?
                $olganOdam = null;
                $kelganlar = 0;
                $kelmaganlar = 0;

                if ($davomatOlindi) {
                    $birinchiRecord = $paraDavomat->first();
                    $olganOdam = $birinchiRecord->xodim;
                    $kelganlar = $paraDavomat->where($paraField, 'bor')->count();
                    $kelmaganlar = $paraDavomat->where($paraField, 'yoq')->count();
                }

                $paraData[$para] = [
                    'tugadi' => $paraTugadi,
                    'olindi' => $davomatOlindi,
                    'olgan_odam' => $olganOdam,
                    'kelganlar' => $kelganlar,
                    'kelmaganlar' => $kelmaganlar,
                    'jami' => $guruh->talabalar->count(),
                ];
            }

            $guruhlarData[] = [
                'guruh' => $guruh,
                'paralar' => $paraData,
            ];
        }

        // Umumiy statistika - har bir para uchun
        $umumiyStatistika = [];
        foreach ([1, 2, 3, 4] as $para) {
            $paraTugadi = ParaVaqtlari::paraTugadimi($para);
            $olinganGuruhlar = 0;
            $olinmaganGuruhlar = 0;
            $jamiKeldi = 0;
            $jamiKelmadi = 0;
            $kurs1Keldi = 0;
            $kurs1Jami = 0;
            $kurs2Keldi = 0;
            $kurs2Jami = 0;

            foreach ($guruhlarData as $data) {
                $paraInfo = $data['paralar'][$para];
                $guruh = $data['guruh'];

                if ($paraInfo['olindi']) {
                    $olinganGuruhlar++;
                    $jamiKeldi += $paraInfo['kelganlar'];
                    $jamiKelmadi += $paraInfo['kelmaganlar'];

                    if ($guruh->kurs == 1) {
                        $kurs1Keldi += $paraInfo['kelganlar'];
                        $kurs1Jami += $paraInfo['kelganlar'] + $paraInfo['kelmaganlar'];
                    } else {
                        $kurs2Keldi += $paraInfo['kelganlar'];
                        $kurs2Jami += $paraInfo['kelganlar'] + $paraInfo['kelmaganlar'];
                    }
                } else {
                    $olinmaganGuruhlar++;
                }
            }

            $umumiyStatistika[$para] = [
                'tugadi' => $paraTugadi,
                'olingan' => $olinganGuruhlar,
                'olinmagan' => $olinmaganGuruhlar,
                'jami_guruh' => $guruhlar->count(),
                'jami_keldi' => $jamiKeldi,
                'jami_kelmadi' => $jamiKelmadi,
                'kurs1_keldi' => $kurs1Keldi,
                'kurs1_jami' => $kurs1Jami,
                'kurs1_foiz' => $kurs1Jami > 0 ? round(($kurs1Keldi / $kurs1Jami) * 100, 1) : 0,
                'kurs2_keldi' => $kurs2Keldi,
                'kurs2_jami' => $kurs2Jami,
                'kurs2_foiz' => $kurs2Jami > 0 ? round(($kurs2Keldi / $kurs2Jami) * 100, 1) : 0,
            ];
        }

        return view('davomat.hisobot', compact(
            'sana',
            'paralar',
            'guruhlarData',
            'davomatOluvchilar',
            'umumiyStatistika',
            'hozirgiVaqt'
        ));
    }

    /**
     * Para bo'yicha batafsil hisobot - AJAX uchun
     */
    public function paraHisobot(Request $request)
    {
        $sana = $request->get('sana', ParaVaqtlari::bugungiSana());
        $para = $request->get('para', 1);

        // Bu paradagi davomatlar
        $davomatlar = Davomat::where('sana', $sana)
            ->where('para', $para)
            ->with(['guruh', 'talaba', 'yaratuvchi'])
            ->get()
            ->groupBy('guruh_id');

        // Faqat 1-kurs va 2-kurs guruhlar
        $guruhlar = Guruh::whereIn('kurs', [1, 2])
            ->with(['talabalar' => function($q) {
                $q->where('faol', true);
            }])
            ->orderBy('kurs')
            ->orderBy('nomi')
            ->get();

        $result = [];

        foreach ($guruhlar as $guruh) {
            $guruhDavomat = $davomatlar[$guruh->id] ?? collect([]);

            $result[] = [
                'guruh_id' => $guruh->id,
                'guruh_nomi' => $guruh->nomi,
                'kurs' => $guruh->kurs,
                'jami_talabalar' => $guruh->talabalar->count(),
                'davomat_olindi' => $guruhDavomat->isNotEmpty(),
                'kelganlar' => $guruhDavomat->where('holat', 'keldi')->count(),
                'kelmaganlar' => $guruhDavomat->where('holat', 'kelmadi')->count(),
                'olgan_odam' => $guruhDavomat->first()?->yaratuvchi?->name,
                'olingan_vaqt' => $guruhDavomat->first()?->created_at?->format('H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'para' => $para,
            'sana' => $sana,
        ]);
    }

    /**
     * Davomat oluvchi faoliyati - alohida odam bo'yicha
     */
    public function davomatOluvchiFaoliyati(Request $request, User $user)
    {
        $boshlanish = $request->get('boshlanish', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tugash = $request->get('tugash', ParaVaqtlari::bugungiSana());

        // Bu foydalanuvchi olgan davomatlar (xodim_id ustuni)
        $davomatlar = Davomat::where('xodim_id', $user->id)
            ->whereBetween('sana', [$boshlanish, $tugash])
            ->with(['guruh'])
            ->get()
            ->groupBy('sana');

        // Statistika
        $kunlar = [];
        $currentDate = Carbon::parse($boshlanish);
        $endDate = Carbon::parse($tugash);

        while ($currentDate <= $endDate) {
            $sana = $currentDate->format('Y-m-d');
            $kunDavomatlari = $davomatlar[$sana] ?? collect([]);

            $paralar = [];
            foreach ([1, 2, 3, 4] as $para) {
                $paraField = 'para_' . $para;
                // Bu parada davomat olingan guruhlar
                $paraGuruhlar = $kunDavomatlari->filter(fn($d) => $d->$paraField !== null)->unique('guruh_id');

                $paralar[$para] = [
                    'guruhlar_soni' => $paraGuruhlar->count(),
                    'guruhlar' => $paraGuruhlar->map(fn($d) => $d->guruh?->nomi)->filter()->values()->toArray(),
                ];
            }

            $kunlar[$sana] = [
                'paralar' => $paralar,
                'jami' => $kunDavomatlari->unique('guruh_id')->count(),
            ];

            $currentDate->addDay();
        }

        return view('davomat.oluvchi-faoliyati', compact('user', 'kunlar', 'boshlanish', 'tugash'));
    }
}
