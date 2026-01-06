<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class FoydalanuvchiController extends Controller
{
    /**
     * Foydalanuvchilar ro'yxati
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Qidiruv
        if ($request->filled('qidiruv')) {
            $qidiruv = $request->input('qidiruv');
            $query->where(function ($q) use ($qidiruv) {
                $q->where('name', 'like', "%{$qidiruv}%")
                    ->orWhere('email', 'like', "%{$qidiruv}%");
            });
        }
        
        // Rol bo'yicha filter
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        
        $foydalanuvchilar = $query->orderBy('name')->paginate(20);
        
        return view('foydalanuvchilar.index', compact('foydalanuvchilar'));
    }

    /**
     * Yangi foydalanuvchi yaratish formasi
     */
    public function create()
    {
        return view('foydalanuvchilar.create');
    }

    /**
     * Yangi foydalanuvchini saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => 'required|in:admin,davomat_oluvchi,koruvchi',
        ], [
            'name.required' => 'Ism kiritilishi shart',
            'email.required' => 'Email kiritilishi shart',
            'email.email' => 'Email formati noto\'g\'ri',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.required' => 'Parol kiritilishi shart',
            'password.confirmed' => 'Parollar mos kelmadi',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'role.required' => 'Rol tanlanishi shart',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('foydalanuvchilar.index')
            ->with('muvaffaqiyat', 'Foydalanuvchi muvaffaqiyatli yaratildi.');
    }

    /**
     * Foydalanuvchini tahrirlash formasi
     */
    public function edit(User $foydalanuvchi)
    {
        return view('foydalanuvchilar.edit', compact('foydalanuvchi'));
    }

    /**
     * Foydalanuvchini yangilash
     */
    public function update(Request $request, User $foydalanuvchi)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($foydalanuvchi->id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'role' => 'required|in:admin,davomat_oluvchi,koruvchi',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Ism kiritilishi shart',
            'email.required' => 'Email kiritilishi shart',
            'email.email' => 'Email formati noto\'g\'ri',
            'email.unique' => 'Bu email allaqachon ro\'yxatdan o\'tgan',
            'password.confirmed' => 'Parollar mos kelmadi',
            'password.min' => 'Parol kamida 6 ta belgidan iborat bo\'lishi kerak',
            'role.required' => 'Rol tanlanishi shart',
        ]);

        // Parol kiritilgan bo'lsa yangilash
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $validated['is_active'] = $request->boolean('is_active', true);

        $foydalanuvchi->update($validated);

        return redirect()->route('foydalanuvchilar.index')
            ->with('muvaffaqiyat', 'Foydalanuvchi muvaffaqiyatli yangilandi.');
    }

    /**
     * Foydalanuvchini o'chirish
     */
    public function destroy(User $foydalanuvchi)
    {
        // O'zini o'chirishga yo'l qo'ymaslik
        if ($foydalanuvchi->id === auth()->id()) {
            return back()->with('xato', 'O\'zingizni o\'chira olmaysiz.');
        }
        
        // Davomat tarixi borligini tekshirish
        if ($foydalanuvchi->davomatlar()->count() > 0) {
            return back()->with('xato', 'Bu foydalanuvchi davomat olgan. O\'chirish mumkin emas.');
        }

        $foydalanuvchi->delete();

        return redirect()->route('foydalanuvchilar.index')
            ->with('muvaffaqiyat', 'Foydalanuvchi muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Foydalanuvchi holatini o'zgartirish
     */
    public function toggleStatus(User $foydalanuvchi)
    {
        // O'zini o'zgartirishga yo'l qo'ymaslik
        if ($foydalanuvchi->id === auth()->id()) {
            return back()->with('xato', 'O\'zingizning holatingizni o\'zgartira olmaysiz.');
        }
        
        $foydalanuvchi->update([
            'is_active' => !$foydalanuvchi->is_active,
        ]);
        
        $holat = $foydalanuvchi->is_active ? 'faollashtirildi' : 'nofaol qilindi';

        return back()->with('muvaffaqiyat', "Foydalanuvchi {$holat}.");
    }
}
