<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DavomatOluvchiMiddleware
{
    /**
     * Davomat olish huquqi borlarni o'tkazish
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->canTakeAttendance()) {
            abort(403, 'Sizda davomat olish huquqi yo\'q.');
        }

        return $next($request);
    }
}
