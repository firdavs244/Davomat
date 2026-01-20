<?php

namespace App\Http\Controllers;

use App\Models\Guruh;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuruhController extends Controller
{
    /**
     * Guruhlar ro'yxati
     */
    public function index(Request $request)
    {
        $query = Guruh::withCount('aktivTalabalar');

        // Qidiruv
        if ($request->filled('qidiruv')) {
            $qidiruv = $request->input('qidiruv');
            $query->where(function ($q) use ($qidiruv) {
                $q->where('nomi', 'like', "%{$qidiruv}%")
                    ->orWhere('yunalish', 'like', "%{$qidiruv}%");
            });
        }

        // Kurs bo'yicha filter
        if ($request->filled('kurs')) {
            $query->where('kurs', $request->input('kurs'));
        }

        // Holat bo'yicha filter
        if ($request->filled('holat')) {
            $isActive = $request->input('holat') === 'aktiv';
            $query->where('is_active', $isActive);
        }

        $guruhlar = $query->orderBy('nomi')->paginate(15);

        // Filterlar uchun ma'lumotlar (kollejda faqat 1 va 2-kurs)
        $kurslar = [1, 2];
        $yunalishlar = Guruh::distinct()->pluck('yunalish');

        return view('guruhlar.index', compact('guruhlar', 'kurslar', 'yunalishlar'));
    }

    /**
     * Yangi guruh yaratish formasi
     */
    public function create()
    {
        $yunalishlar = Guruh::distinct()->pluck('yunalish');
        return view('guruhlar.create', compact('yunalishlar'));
    }

    /**
     * Yangi guruhni saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomi' => 'required|string|max:50|unique:guruhlar,nomi',
            'kurs' => 'required|integer|min:1|max:2',
            'yunalish' => 'required|string|max:100',
        ], [
            'nomi.required' => 'Guruh nomi kiritilishi shart',
            'nomi.unique' => 'Bu nomli guruh allaqachon mavjud',
            'nomi.max' => 'Guruh nomi 50 ta belgidan oshmasligi kerak',
            'kurs.required' => 'Kurs tanlanishi shart',
            'kurs.min' => 'Kurs 1 dan kam bo\'lishi mumkin emas',
            'kurs.max' => 'Kurs 2 dan oshishi mumkin emas (kollejda faqat 1 va 2-kurs)',
            'yunalish.required' => 'Yo\'nalish kiritilishi shart',
        ]);

        Guruh::create($validated);

        return redirect()->route('guruhlar.index')
            ->with('muvaffaqiyat', 'Guruh muvaffaqiyatli yaratildi.');
    }

    /**
     * Guruhni ko'rish
     */
    public function show(Guruh $guruh)
    {
        $guruh->load(['talabalar' => function ($query) {
            $query->orderBy('fish');
        }]);

        return view('guruhlar.show', compact('guruh'));
    }

    /**
     * Guruhni tahrirlash formasi
     */
    public function edit(Guruh $guruh)
    {
        $yunalishlar = Guruh::distinct()->pluck('yunalish');
        return view('guruhlar.edit', compact('guruh', 'yunalishlar'));
    }

    /**
     * Guruhni yangilash
     */
    public function update(Request $request, Guruh $guruh)
    {
        $validated = $request->validate([
            'nomi' => [
                'required',
                'string',
                'max:50',
                Rule::unique('guruhlar', 'nomi')->ignore($guruh->id),
            ],
            'kurs' => 'required|integer|min:1|max:2',
            'yunalish' => 'required|string|max:100',
        ], [
            'nomi.required' => 'Guruh nomi kiritilishi shart',
            'nomi.unique' => 'Bu nomli guruh allaqachon mavjud',
            'kurs.required' => 'Kurs tanlanishi shart',
            'yunalish.required' => 'Yo\'nalish kiritilishi shart',
        ]);

        // is_active ni alohida qo'shamiz
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        $guruh->update($validated);

        return redirect()->route('guruhlar.index')
            ->with('muvaffaqiyat', 'Guruh muvaffaqiyatli yangilandi.');
    }

    /**
     * Guruhni o'chirish
     */
    public function destroy(Guruh $guruh)
    {
        // Talabalar borligini tekshirish
        if ($guruh->talabalar()->count() > 0) {
            return back()->with('xato', 'Bu guruhda talabalar mavjud. Avval talabalarni boshqa guruhga o\'tkazing.');
        }

        $guruh->delete();

        return redirect()->route('guruhlar.index')
            ->with('muvaffaqiyat', 'Guruh muvaffaqiyatli o\'chirildi.');
    }

    /**
     * AJAX: Guruh talabalarini olish
     */
    public function getTalabalar(Guruh $guruh)
    {
        $talabalar = $guruh->aktivTalabalar()
            ->orderBy('fish')
            ->get(['id', 'fish', 'holati']);

        return response()->json($talabalar);
    }
}
