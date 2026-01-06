<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Foydalanuvchi olgan davomatlar
     */
    public function davomatlar(): HasMany
    {
        return $this->hasMany(Davomat::class, 'xodim_id');
    }

    /**
     * Admin ekanligini tekshirish
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Davomat oluvchi ekanligini tekshirish
     */
    public function isDavomatOluvchi(): bool
    {
        return $this->role === 'davomat_oluvchi';
    }

    /**
     * Ko'ruvchi ekanligini tekshirish
     */
    public function isKoruvchi(): bool
    {
        return $this->role === 'koruvchi';
    }

    /**
     * Davomat olish huquqi borligini tekshirish
     */
    public function canTakeAttendance(): bool
    {
        return in_array($this->role, ['admin', 'davomat_oluvchi']);
    }

    /**
     * Tahrirlash huquqi borligini tekshirish
     */
    public function canEdit(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Export qilish huquqi borligini tekshirish
     */
    public function canExport(): bool
    {
        return in_array($this->role, ['admin', 'davomat_oluvchi']);
    }

    /**
     * Role nomini o'zbek tilida qaytarish
     */
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'davomat_oluvchi' => 'Davomat Oluvchi',
            'koruvchi' => "Ko'ruvchi",
            default => 'Noma\'lum',
        };
    }
}
