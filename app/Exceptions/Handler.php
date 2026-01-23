<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // 404 sahifalar uchun role ga qarab yo'naltirish
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sahifa topilmadi'], 404);
            }

            $user = auth()->user();

            if (!$user) {
                return redirect()->route('login')->with('xato', 'Sahifa topilmadi. Tizimga kiring.');
            }

            // Role ga qarab tegishli sahifaga yo'naltirish
            $redirectRoute = match($user->role) {
                'admin' => 'dashboard',
                'davomat_oluvchi' => 'davomat.olish',
                'koruvchi' => 'dashboard',
                default => 'dashboard',
            };

            return redirect()->route($redirectRoute)->with('xato', 'So\'ralgan sahifa topilmadi.');
        });
    }
}
