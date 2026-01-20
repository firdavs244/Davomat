<?php

namespace App\Http\Middleware;

use App\Helpers\ParaVaqtlari;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DavomatVaqtMiddleware
{
    /**
     * Davomat olish vaqtini tekshirish
     * Faqat davomat_oluvchi uchun vaqt cheklovi qo'llaniladi
     * Admin har qanday vaqtda tahrirlashi mumkin
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admin uchun cheklov yo'q
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Davomat saqlash so'rovi tekshiriladi
        if ($request->isMethod('POST') && $request->routeIs('davomat.saqlash')) {
            $sana = $request->input('sana');
            $para = (int) $request->input('para');
            $guruhId = $request->input('guruh_id');

            // Faqat bugungi kun uchun davomat olish mumkin
            if ($sana !== ParaVaqtlari::bugungiSana()) {
                return back()->with('xato', 'Faqat bugungi kun uchun davomat olish mumkin.');
            }

            // Para tugagan bo'lishi kerak
            if (!ParaVaqtlari::davomatOlishMumkinmi($para, $sana)) {
                return back()->with('xato', ParaVaqtlari::paraNomi($para) . ' hali tugamagan. Para tugaganidan keyin davomat olishingiz mumkin.');
            }

            // Bu guruh uchun bu para uchun davomat olinganmi?
            if ($guruhId && $sana && $para) {
                $mavjudDavomat = \App\Models\Davomat::where('guruh_id', $guruhId)
                    ->where('sana', $sana)
                    ->whereNotNull('para_' . $para)
                    ->exists();

                if ($mavjudDavomat) {
                    return back()->with('xato', 'Bu guruh uchun ' . $para . '-para davomati allaqachon olingan. Bir marta davomat olgandan keyin o\'zgartirish mumkin emas.');
                }
            }
        }

        return $next($request);
    }
}
