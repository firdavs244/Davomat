<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Foydalanuvchi rolini tekshirish
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Ruxsat berilgan rollar
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Foydalanuvchi tizimga kirganligini tekshirish
        if (!$request->user()) {
            return redirect()->route('login')
                ->with('xato', 'Iltimos, tizimga kiring.');
        }

        // Foydalanuvchi aktiv ekanligini tekshirish
        if (!$request->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('xato', 'Sizning hisobingiz faol emas. Administrator bilan bog\'laning.');
        }

        // Rolni tekshirish
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Sizda bu sahifaga kirish huquqi yo\'q.');
        }

        return $next($request);
    }
}
