<?php

namespace App\Http\Controllers;

use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DavomatController extends Controller
{
    /**
     * Davomat olish sahifasi
     */
    public function olish(Request $request)
    {
        $sana = $request->input('sana', now()->toDateString());
        $para = $request->input('para', 1);
        $guruhId = $request->input('guruh_id');
        
        $guruhlar = Guruh::aktiv()->orderBy('nomi')->get();
        
        $talabalar = collect();
        $mavjudDavomat = collect();
        
        if ($guruhId) {
            // Faqat shu kunda kollej talabasi bo'lganlarni olish
            $sanaCarbonObj = Carbon::parse($sana);
            
            $talabalar = Talaba::aktiv()
                ->where('guruh_id', $guruhId)
                ->where('kirgan_sana', '<=', $sana)
                ->where(function ($q) use ($sana) {
                    $q->whereNull('ketgan_sana')
                        ->orWhere('ketgan_sana', '>=', $sana);
                })
                ->orderBy('fish')
                ->get();
            
            // Mavjud davomat ma'lumotlarini olish
            $mavjudDavomat = Davomat::where('guruh_id', $guruhId)
                ->where('sana', $sana)
                ->pluck('para_' . $para, 'talaba_id');
        }
        
        return view('davomat.olish', compact(
            'guruhlar',
            'talabalar',
            'mavjudDavomat',
            'sana',
            'para',
            'guruhId'
        ));
    }

    /**
     * Davomatni saqlash
     */
    public function saqlash(Request $request)
    {
        $validated = $request->validate([
            'guruh_id' => 'required|exists:guruhlar,id',
            'sana' => 'required|date',
            'para' => 'required|in:1,2,3',
            'davomat' => 'nullable|array',
            'davomat.*' => 'required|in:bor,yoq',
        ], [
            'guruh_id.required' => 'Guruh tanlanishi shart',
            'sana.required' => 'Sana kiritilishi shart',
            'para.required' => 'Para tanlanishi shart',
        ]);

        // Agar hech qanday davomat belgilanmagan bo'lsa
        if (!isset($validated['davomat']) || empty($validated['davomat'])) {
            return back()->with('xato', 'Kamida bitta talaba uchun davomat belgilang.');
        }

        $para = 'para_' . $validated['para'];
        $xodimId = auth()->id();
        
        DB::beginTransaction();
        
        try {
            foreach ($validated['davomat'] as $talabaId => $holat) {
                // Talabani tekshirish
                $talaba = Talaba::find($talabaId);
                if (!$talaba || $talaba->guruh_id != $validated['guruh_id']) {
                    continue;
                }
                
                // Davomat yozuvini topish yoki yaratish
                $davomat = Davomat::firstOrNew([
                    'talaba_id' => $talabaId,
                    'sana' => $validated['sana'],
                ]);
                
                $davomat->guruh_id = $validated['guruh_id'];
                $davomat->$para = $holat;
                $davomat->xodim_id = $xodimId;
                $davomat->save();
            }
            
            DB::commit();
            
            return redirect()->route('davomat.olish', [
                'guruh_id' => $validated['guruh_id'],
                'sana' => $validated['sana'],
                'para' => $validated['para'],
            ])->with('muvaffaqiyat', $validated['para'] . '-para davomati muvaffaqiyatli saqlandi!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('xato', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Davomat tarixi
     */
    public function tarixi(Request $request)
    {
        $query = Davomat::with(['talaba', 'guruh', 'xodim']);
        
        // Guruh bo'yicha filter
        if ($request->filled('guruh_id')) {
            $query->where('guruh_id', $request->input('guruh_id'));
        }
        
        // Sana bo'yicha filter
        if ($request->filled('sana_dan')) {
            $query->where('sana', '>=', $request->input('sana_dan'));
        }
        
        if ($request->filled('sana_gacha')) {
            $query->where('sana', '<=', $request->input('sana_gacha'));
        }
        
        // Talaba bo'yicha qidiruv
        if ($request->filled('talaba')) {
            $talaba = $request->input('talaba');
            $query->whereHas('talaba', function ($q) use ($talaba) {
                $q->where('fish', 'like', "%{$talaba}%");
            });
        }
        
        $davomatlar = $query->orderByDesc('sana')
            ->orderBy('guruh_id')
            ->paginate(30);
        
        $guruhlar = Guruh::aktiv()->orderBy('nomi')->get();
        
        return view('davomat.tarixi', compact('davomatlar', 'guruhlar'));
    }

    /**
     * Foydalanuvchining o'z davomat tarixi
     */
    public function meningTarixim(Request $request)
    {
        $query = Davomat::with(['talaba', 'guruh'])
            ->where('xodim_id', auth()->id());
        
        // Sana bo'yicha filter
        if ($request->filled('sana_dan')) {
            $query->where('sana', '>=', $request->input('sana_dan'));
        }
        
        if ($request->filled('sana_gacha')) {
            $query->where('sana', '<=', $request->input('sana_gacha'));
        }
        
        $davomatlar = $query->orderByDesc('sana')->paginate(30);
        
        return view('davomat.mening-tarixim', compact('davomatlar'));
    }

    /**
     * Davomatni tahrirlash (faqat admin)
     */
    public function edit(Davomat $davomat)
    {
        $davomat->load(['talaba', 'guruh']);
        return view('davomat.edit', compact('davomat'));
    }

    /**
     * Davomatni yangilash (faqat admin)
     */
    public function update(Request $request, Davomat $davomat)
    {
        $validated = $request->validate([
            'para_1' => 'nullable|in:bor,yoq',
            'para_2' => 'nullable|in:bor,yoq',
            'para_3' => 'nullable|in:bor,yoq',
            'izoh' => 'nullable|string|max:500',
        ]);

        $davomat->update($validated);

        return redirect()->route('davomat.tarixi')
            ->with('muvaffaqiyat', 'Davomat muvaffaqiyatli yangilandi.');
    }

    /**
     * Davomatni o'chirish (faqat admin)
     */
    public function destroy(Davomat $davomat)
    {
        $davomat->delete();

        return redirect()->route('davomat.tarixi')
            ->with('muvaffaqiyat', 'Davomat muvaffaqiyatli o\'chirildi.');
    }

    /**
     * AJAX: Guruh davomati holatini olish
     */
    public function getGuruhDavomat(Request $request)
    {
        $guruhId = $request->input('guruh_id');
        $sana = $request->input('sana', now()->toDateString());
        
        $davomatlar = Davomat::where('guruh_id', $guruhId)
            ->where('sana', $sana)
            ->get(['talaba_id', 'para_1', 'para_2', 'para_3']);
        
        return response()->json($davomatlar);
    }
}
