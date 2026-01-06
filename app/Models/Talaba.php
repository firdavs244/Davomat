<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Talaba extends Model
{
    use HasFactory;

    /**
     * Jadval nomi
     */
    protected $table = 'talabalar';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'fish',
        'guruh_id',
        'kirgan_sana',
        'ketgan_sana',
        'holati',
        'izoh',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'kirgan_sana' => 'date',
        'ketgan_sana' => 'date',
    ];

    /**
     * Talaba guruhi
     */
    public function guruh(): BelongsTo
    {
        return $this->belongsTo(Guruh::class, 'guruh_id');
    }

    /**
     * Talaba davomatlari
     */
    public function davomatlar(): HasMany
    {
        return $this->hasMany(Davomat::class, 'talaba_id');
    }

    /**
     * Talaba ma'lum bir kunda kollej talabasi ekanligini tekshirish
     */
    public function isKollejTalabasi(Carbon|string|null $sana = null): bool
    {
        if (is_string($sana)) {
            $sana = Carbon::parse($sana);
        }
        $sana = $sana ?? now();
        
        // Kirgan sanadan oldin bo'lsa - talaba emas
        if ($sana->lt($this->kirgan_sana)) {
            return false;
        }
        
        // Ketgan sanadan keyin bo'lsa - talaba emas
        if ($this->ketgan_sana && $sana->gt($this->ketgan_sana)) {
            return false;
        }
        
        return true;
    }

    /**
     * Talabaning davomati olinishi mumkinligini tekshirish
     */
    public function canTakeAttendance(?Carbon $sana = null): bool
    {
        return $this->holati === 'aktiv' && $this->isKollejTalabasi($sana);
    }

    /**
     * Scope: Faqat aktiv talabalar
     */
    public function scopeAktiv($query)
    {
        return $query->where('holati', 'aktiv');
    }

    /**
     * Scope: Faqat noaktiv talabalar
     */
    public function scopeNoaktiv($query)
    {
        return $query->where('holati', 'noaktiv');
    }

    /**
     * Scope: Guruh bo'yicha filter
     */
    public function scopeGuruh($query, int $guruhId)
    {
        return $query->where('guruh_id', $guruhId);
    }

    /**
     * Scope: Ma'lum sana uchun kollej talabasi bo'lganlar
     */
    public function scopeKollejTalabasi($query, ?Carbon $sana = null)
    {
        $sana = $sana ?? now();
        
        return $query->where('kirgan_sana', '<=', $sana)
            ->where(function ($q) use ($sana) {
                $q->whereNull('ketgan_sana')
                    ->orWhere('ketgan_sana', '>=', $sana);
            });
    }

    /**
     * Talabaning jami yo'qlik statistikasi
     */
    public function getYoqlikStatistikasiAttribute(): array
    {
        $davomatlar = $this->davomatlar;
        
        $jamiParalar = 0;
        $yoqParalar = 0;
        
        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para !== null) {
                    $jamiParalar++;
                    if ($d->$para === 'yoq') {
                        $yoqParalar++;
                    }
                }
            }
        }
        
        $foiz = $jamiParalar > 0 ? round(($yoqParalar / $jamiParalar) * 100, 1) : 0;
        
        return [
            'jami_paralar' => $jamiParalar,
            'yoq_paralar' => $yoqParalar,
            'yoqlik_foizi' => $foiz,
        ];
    }

    /**
     * Talabaning ma'lum bir oydagi yo'qliklari
     */
    public function getOylikYoqliklar(int $yil, int $oy): int
    {
        $davomatlar = $this->davomatlar()
            ->whereYear('sana', $yil)
            ->whereMonth('sana', $oy)
            ->get();
        
        $yoqlar = 0;
        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para === 'yoq') {
                    $yoqlar++;
                }
            }
        }
        
        return $yoqlar;
    }

    /**
     * Holat nomini o'zbek tilida qaytarish
     */
    public function getHolatNomiAttribute(): string
    {
        return match($this->holati) {
            'aktiv' => 'Aktiv',
            'noaktiv' => 'Noaktiv',
            default => 'Noma\'lum',
        };
    }
}
