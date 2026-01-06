<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guruh extends Model
{
    use HasFactory;

    /**
     * Jadval nomi
     */
    protected $table = 'guruhlar';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'nomi',
        'kurs',
        'yunalish',
        'is_active',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'kurs' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Guruhdagi talabalar
     */
    public function talabalar(): HasMany
    {
        return $this->hasMany(Talaba::class, 'guruh_id');
    }

    /**
     * Faqat aktiv talabalar
     */
    public function aktivTalabalar(): HasMany
    {
        return $this->hasMany(Talaba::class, 'guruh_id')->where('holati', 'aktiv');
    }

    /**
     * Guruh davomatlari
     */
    public function davomatlar(): HasMany
    {
        return $this->hasMany(Davomat::class, 'guruh_id');
    }

    /**
     * Aktiv talabalar sonini olish
     */
    public function getAktivTalabalarSoniAttribute(): int
    {
        return $this->aktivTalabalar()->count();
    }

    /**
     * Jami talabalar sonini olish
     */
    public function getJamiTalabalarSoniAttribute(): int
    {
        return $this->talabalar()->count();
    }

    /**
     * Guruhning to'liq nomini olish
     */
    public function getToliqNomiAttribute(): string
    {
        return $this->nomi . ' (' . $this->kurs . '-kurs, ' . $this->yunalish . ')';
    }

    /**
     * Scope: Faqat aktiv guruhlar
     */
    public function scopeAktiv($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Kurs bo'yicha filter
     */
    public function scopeKurs($query, int $kurs)
    {
        return $query->where('kurs', $kurs);
    }

    /**
     * Scope: Yo'nalish bo'yicha filter
     */
    public function scopeYunalish($query, string $yunalish)
    {
        return $query->where('yunalish', $yunalish);
    }

    /**
     * Guruhni o'chirish mumkinligini tekshirish
     */
    public function canBeDeleted(): bool
    {
        return $this->talabalar()->count() === 0;
    }

    /**
     * Bugungi davomat statistikasi
     */
    public function getBugungiStatistikaAttribute(): array
    {
        $bugun = now()->toDateString();
        $aktivTalabalar = $this->aktivTalabalar()->count();

        $davomatlar = $this->davomatlar()
            ->where('sana', $bugun)
            ->get();

        $borlar = 0;
        $yoqlar = 0;

        foreach ($davomatlar as $d) {
            // Har bir para uchun hisoblash
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para === 'bor') $borlar++;
                elseif ($d->$para === 'yoq') $yoqlar++;
            }
        }

        $jami = $borlar + $yoqlar;
        $foiz = $jami > 0 ? round(($borlar / $jami) * 100, 1) : 0;

        return [
            'talabalar_soni' => $aktivTalabalar,
            'bor' => $borlar,
            'yoq' => $yoqlar,
            'foiz' => $foiz,
        ];
    }
}
