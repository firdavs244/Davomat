<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login sahifasini ko'rsatish
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Login jarayoni
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email kiritilishi shart',
            'email.email' => 'Email formati noto\'g\'ri',
            'password.required' => 'Parol kiritilishi shart',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Aktiv emasligini tekshirish
            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Sizning hisobingiz faol emas. Administrator bilan bog\'laning.',
                ]);
            }

            $request->session()->regenerate();

            // Rolga qarab yo'naltirish
            if ($user->isDavomatOluvchi()) {
                // Davomat oluvchi foydalanuvchilarni to'g'ridan-to'g'ri davomat olish sahifasiga yo'naltirish
                return redirect()->route('davomat.olish')
                    ->with('muvaffaqiyat', 'Xush kelibsiz, ' . $user->name . '!');
            }

            // Boshqa foydalanuvchilarni dashboard ga yo'naltirish
            return redirect()->intended(route('dashboard'))
                ->with('muvaffaqiyat', 'Xush kelibsiz, ' . $user->name . '!');
        }

        throw ValidationException::withMessages([
            'email' => 'Kiritilgan ma\'lumotlar noto\'g\'ri.',
        ]);
    }

    /**
     * Logout jarayoni
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('muvaffaqiyat', 'Tizimdan muvaffaqiyatli chiqdingiz.');
    }
}
