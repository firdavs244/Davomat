<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Davomat oluvchi foydalanuvchilar uchun sahifa cheklash middleware
 * Faqat davomat olish va mening tarixim sahifalariga ruxsat beradi
 */
class DavomatOluvchiRestrict
{
    /**
     * Davomat oluvchi uchun ruxsat etilgan route'lar
     */
    protected array $allowedRoutes = [
        'davomat.olish',
        'davomat.saqlash',
        'davomat.mening-tarixim',
        'davomat.guruh-davomat',
        'davomat.guruhlar-qidirish',
        'davomat.para-holati',
        'login',
        'login.post',
        'logout',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Agar foydalanuvchi login qilmagan bo'lsa, o'tkazib yuborish
        if (!$user) {
            return $next($request);
        }

        // Agar foydalanuvchi davomat_oluvchi bo'lmasa, o'tkazib yuborish
        if (!$user->isDavomatOluvchi()) {
            return $next($request);
        }

        // Hozirgi route nomini olish
        $currentRoute = $request->route()->getName();

        // Agar ruxsat etilgan route bo'lsa, o'tkazish
        if (in_array($currentRoute, $this->allowedRoutes)) {
            return $next($request);
        }

        // Ruxsat berilmagan sahifaga kirishga urinish - davomat olish sahifasiga yo'naltirish
        return redirect()->route('davomat.olish')
            ->with('ogohlantirish', 'Sizga faqat davomat olish sahifasiga kirish ruxsati berilgan.');
    }
}
