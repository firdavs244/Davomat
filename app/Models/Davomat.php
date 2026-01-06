<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Davomat extends Model
{
    use HasFactory;

    /**
     * Jadval nomi
     */
    protected $table = 'davomat';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'talaba_id',
        'guruh_id',
        'sana',
        'para_1',
        'para_2',
        'para_3',
        'xodim_id',
        'izoh',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'sana' => 'date',
    ];

    /**
     * Davomat talabasi
     */
    public function talaba(): BelongsTo
    {
        return $this->belongsTo(Talaba::class, 'talaba_id');
    }

    /**
     * Davomat guruhi
     */
    public function guruh(): BelongsTo
    {
        return $this->belongsTo(Guruh::class, 'guruh_id');
    }

    /**
     * Davomat olgan xodim
     */
    public function xodim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'xodim_id');
    }

    /**
     * Jami yo'qliklar soni
     */
    public function getJamiYoqAttribute(): int
    {
        $yoq = 0;
        if ($this->para_1 === 'yoq') $yoq++;
        if ($this->para_2 === 'yoq') $yoq++;
        if ($this->para_3 === 'yoq') $yoq++;
        return $yoq;
    }

    /**
     * Jami bor soni
     */
    public function getJamiBorAttribute(): int
    {
        $bor = 0;
        if ($this->para_1 === 'bor') $bor++;
        if ($this->para_2 === 'bor') $bor++;
        if ($this->para_3 === 'bor') $bor++;
        return $bor;
    }

    /**
     * Para qiymati o'zbek tilida
     */
    public static function paraQiymati(?string $value): string
    {
        return match($value) {
            'bor' => 'Bor',
            'yoq' => "Yo'q",
            null => '-',
            default => '-',
        };
    }

    /**
     * Scope: Bugungi davomat
     */
    public function scopeBugungi($query)
    {
        return $query->where('sana', now()->toDateString());
    }

    /**
     * Scope: Ma'lum sana uchun
     */
    public function scopeSana($query, Carbon|string $sana)
    {
        $sana = $sana instanceof Carbon ? $sana->toDateString() : $sana;
        return $query->where('sana', $sana);
    }

    /**
     * Scope: Ma'lum guruh uchun
     */
    public function scopeGuruh($query, int $guruhId)
    {
        return $query->where('guruh_id', $guruhId);
    }

    /**
     * Scope: Ma'lum hafta uchun
     */
    public function scopeHaftalik($query, ?Carbon $sana = null)
    {
        $sana = $sana ?? now();
        $haftaBoshi = $sana->copy()->startOfWeek();
        $haftaOxiri = $sana->copy()->endOfWeek();
        
        return $query->whereBetween('sana', [$haftaBoshi, $haftaOxiri]);
    }

    /**
     * Scope: Ma'lum oy uchun
     */
    public function scopeOylik($query, ?int $yil = null, ?int $oy = null)
    {
        $yil = $yil ?? now()->year;
        $oy = $oy ?? now()->month;
        
        return $query->whereYear('sana', $yil)->whereMonth('sana', $oy);
    }

    /**
     * Scope: Ma'lum yil uchun
     */
    public function scopeYillik($query, ?int $yil = null)
    {
        $yil = $yil ?? now()->year;
        return $query->whereYear('sana', $yil);
    }

    /**
     * Bugungi davomat statistikasini olish
     */
    public static function bugungiStatistika(): array
    {
        $bugun = now()->toDateString();
        
        $davomatlar = self::where('sana', $bugun)->get();
        
        $jamiParalar = 0;
        $borParalar = 0;
        $yoqParalar = 0;
        
        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para !== null) {
                    $jamiParalar++;
                    if ($d->$para === 'bor') $borParalar++;
                    elseif ($d->$para === 'yoq') $yoqParalar++;
                }
            }
        }
        
        $foiz = $jamiParalar > 0 ? round(($borParalar / $jamiParalar) * 100, 1) : 0;
        
        return [
            'jami_talabalar' => $davomatlar->count(),
            'jami_paralar' => $jamiParalar,
            'bor' => $borParalar,
            'yoq' => $yoqParalar,
            'foiz' => $foiz,
        ];
    }

    /**
     * Haftalik o'rtacha davomat foizi
     */
    public static function haftalikOrtacha(): float
    {
        $haftaBoshi = now()->startOfWeek();
        $haftaOxiri = now()->endOfWeek();
        
        $davomatlar = self::whereBetween('sana', [$haftaBoshi, $haftaOxiri])->get();
        
        $jamiParalar = 0;
        $borParalar = 0;
        
        foreach ($davomatlar as $d) {
            foreach (['para_1', 'para_2', 'para_3'] as $para) {
                if ($d->$para !== null) {
                    $jamiParalar++;
                    if ($d->$para === 'bor') $borParalar++;
                }
            }
        }
        
        return $jamiParalar > 0 ? round(($borParalar / $jamiParalar) * 100, 1) : 0;
    }
}
