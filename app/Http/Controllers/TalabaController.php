<?php

namespace App\Http\Controllers;

use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TalabaController extends Controller
{
    /**
     * Talabalar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Talaba::with('guruh');

        // Qidiruv
        if ($request->filled('qidiruv')) {
            $qidiruv = $request->input('qidiruv');
            $query->where('fish', 'like', "%{$qidiruv}%");
        }

        // Guruh bo'yicha filter
        if ($request->filled('guruh_id')) {
            $query->where('guruh_id', $request->input('guruh_id'));
        }

        // Holat bo'yicha filter
        if ($request->filled('holat')) {
            $query->where('holati', $request->input('holat'));
        }

        $talabalar = $query->orderBy('fish')->paginate(20);

        // Filterlar uchun ma'lumotlar
        $guruhlar = Guruh::aktiv()->orderBy('nomi')->get();

        return view('talabalar.index', compact('talabalar', 'guruhlar'));
    }

    /**
     * Yangi talaba yaratish formasi
     */
    public function create()
    {
        $guruhlar = Guruh::aktiv()->orderBy('nomi')->get();
        return view('talabalar.create', compact('guruhlar'));
    }

    /**
     * Yangi talabani saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fish' => 'required|string|max:150',
            'guruh_id' => 'required|exists:guruhlar,id',
            'kirgan_sana' => 'required|date|before_or_equal:today',
            'izoh' => 'nullable|string|max:500',
        ], [
            'fish.required' => 'Talaba FISH kiritilishi shart',
            'fish.max' => 'FISH 150 ta belgidan oshmasligi kerak',
            'guruh_id.required' => 'Guruh tanlanishi shart',
            'guruh_id.exists' => 'Tanlangan guruh mavjud emas',
            'kirgan_sana.required' => 'Kirgan sana kiritilishi shart',
            'kirgan_sana.date' => 'Sana formati noto\'g\'ri',
            'kirgan_sana.before_or_equal' => 'Kirgan sana bugundan katta bo\'lishi mumkin emas',
        ]);

        Talaba::create($validated);

        return redirect()->route('talabalar.index')
            ->with('muvaffaqiyat', 'Talaba muvaffaqiyatli qo\'shildi.');
    }

    /**
     * Talabani ko'rish
     */
    public function show(Talaba $talaba)
    {
        $talaba->load(['guruh', 'davomatlar' => function ($query) {
            $query->orderByDesc('sana')->limit(30);
        }]);

        // Yo'qlik statistikasi
        $statistika = $talaba->yoqlik_statistikasi;

        return view('talabalar.show', compact('talaba', 'statistika'));
    }

    /**
     * Talabani tahrirlash formasi
     */
    public function edit(Talaba $talaba)
    {
        $guruhlar = Guruh::aktiv()->orderBy('nomi')->get();
        return view('talabalar.edit', compact('talaba', 'guruhlar'));
    }

    /**
     * Talabani yangilash
     */
    public function update(Request $request, Talaba $talaba)
    {
        $validated = $request->validate([
            'fish' => 'required|string|max:150',
            'guruh_id' => 'required|exists:guruhlar,id',
            'kirgan_sana' => 'required|date',
            'ketgan_sana' => 'nullable|date|after:kirgan_sana',
            'holati' => 'required|in:aktiv,noaktiv',
            'izoh' => 'nullable|string|max:500',
        ], [
            'fish.required' => 'Talaba FISH kiritilishi shart',
            'guruh_id.required' => 'Guruh tanlanishi shart',
            'guruh_id.exists' => 'Tanlangan guruh mavjud emas',
            'kirgan_sana.required' => 'Kirgan sana kiritilishi shart',
            'ketgan_sana.after' => 'Ketgan sana kirgan sanadan keyin bo\'lishi kerak',
            'holati.required' => 'Holat tanlanishi shart',
            'holati.in' => 'Holat noto\'g\'ri',
        ]);

        $talaba->update($validated);

        return redirect()->route('talabalar.index')
            ->with('muvaffaqiyat', 'Talaba ma\'lumotlari muvaffaqiyatli yangilandi.');
    }

    /**
     * Talabani "ketdi" deb belgilash
     */
    public function markAsLeft(Request $request, Talaba $talaba)
    {
        $validated = $request->validate([
            'ketgan_sana' => 'required|date|after:' . $talaba->kirgan_sana->format('Y-m-d'),
        ], [
            'ketgan_sana.required' => 'Ketgan sana kiritilishi shart',
            'ketgan_sana.after' => 'Ketgan sana kirgan sanadan keyin bo\'lishi kerak',
        ]);

        $talaba->update([
            'ketgan_sana' => $validated['ketgan_sana'],
            'holati' => 'ketgan',
        ]);

        return back()->with('muvaffaqiyat', 'Talaba "ketdi" deb belgilandi.');
    }

    /**
     * Talabani guruhdan guruhga o'tkazish
     */
    public function transfer(Request $request, Talaba $talaba)
    {
        $validated = $request->validate([
            'yangi_guruh_id' => 'required|exists:guruhlar,id|different:guruh_id',
        ], [
            'yangi_guruh_id.required' => 'Yangi guruh tanlanishi shart',
            'yangi_guruh_id.exists' => 'Tanlangan guruh mavjud emas',
            'yangi_guruh_id.different' => 'Yangi guruh joriy guruhdan farq qilishi kerak',
        ]);

        $eskiGuruh = $talaba->guruh->nomi;
        $talaba->update(['guruh_id' => $validated['yangi_guruh_id']]);
        $yangiGuruh = $talaba->fresh()->guruh->nomi;

        return back()->with('muvaffaqiyat', "Talaba {$eskiGuruh} dan {$yangiGuruh} ga o'tkazildi.");
    }

    /**
     * Talabani o'chirish
     */
    public function destroy(Talaba $talaba)
    {
        // Davomat tarixi borligini tekshirish
        if ($talaba->davomatlar()->count() > 0) {
            return back()->with('xato', 'Bu talabaning davomat tarixi mavjud. O\'chirish mumkin emas.');
        }

        $talaba->delete();

        return redirect()->route('talabalar.index')
            ->with('muvaffaqiyat', 'Talaba muvaffaqiyatli o\'chirildi.');
    }
}
