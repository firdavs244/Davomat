<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Para vaqtlarini boshqarish helper klasi
 * O'zbekiston, Buxoro viloyati vaqti (UTC+5)
 */
class ParaVaqtlari
{
    /**
     * Para vaqtlari (tugatish vaqti asosida)
     * Para tugagandan keyin davomat olish mumkin
     */
    public const PARALAR = [
        1 => [
            'boshlanish' => '08:30',
            'tugash' => '09:50',
            'nomi' => '1-para (08:30 - 09:50)',
        ],
        2 => [
            'boshlanish' => '10:00',
            'tugash' => '11:20',
            'nomi' => '2-para (10:00 - 11:20)',
        ],
        3 => [
            'boshlanish' => '11:30',
            'tugash' => '12:50',
            'nomi' => '3-para (11:30 - 12:50)',
        ],
    ];

    /**
     * Buxoro vaqti timezone
     */
    public const TIMEZONE = 'Asia/Tashkent'; // UTC+5

    /**
     * Paralar ma'lumotini olish
     */
    public static function paralar(): array
    {
        return self::PARALAR;
    }

    /**
     * Hozirgi vaqtni Buxoro vaqtida olish
     */
    public static function hozirgiVaqt(): Carbon
    {
        return Carbon::now(self::TIMEZONE);
    }

    /**
     * Hozirgi sana (Buxoro vaqtida)
     */
    public static function bugungiSana(): string
    {
        return self::hozirgiVaqt()->toDateString();
    }

    /**
     * Berilgan para tugagan yoki tugamagan
     */
    public static function paraTugadimi(int $para): bool
    {
        if (!isset(self::PARALAR[$para])) {
            return false;
        }

        $hozir = self::hozirgiVaqt();
        $tugashVaqti = Carbon::parse(self::bugungiSana() . ' ' . self::PARALAR[$para]['tugash'], self::TIMEZONE);

        return $hozir->greaterThanOrEqualTo($tugashVaqti);
    }

    /**
     * Hozir qaysi para uchun davomat olish mumkin
     * Para tugagandan keyin davomat olish mumkin
     * Keyingi para boshlanmaguncha avvalgi para uchun davomat olish mumkin
     */
    public static function hozirgiDavomatPara(): ?int
    {
        $hozir = self::hozirgiVaqt();
        $bugun = self::bugungiSana();

        // Oxirgi tugagan parani topish
        $davomatPara = null;

        foreach (self::PARALAR as $paraRaqam => $vaqtlar) {
            $tugashVaqti = Carbon::parse($bugun . ' ' . $vaqtlar['tugash'], self::TIMEZONE);

            if ($hozir->greaterThanOrEqualTo($tugashVaqti)) {
                $davomatPara = $paraRaqam;
            }
        }

        return $davomatPara;
    }

    /**
     * Hozir qaysi para davom etyapti
     */
    public static function hozirgiPara(): ?int
    {
        $hozir = self::hozirgiVaqt();
        $bugun = self::bugungiSana();

        foreach (self::PARALAR as $paraRaqam => $vaqtlar) {
            $boshlanish = Carbon::parse($bugun . ' ' . $vaqtlar['boshlanish'], self::TIMEZONE);
            $tugash = Carbon::parse($bugun . ' ' . $vaqtlar['tugash'], self::TIMEZONE);

            if ($hozir->between($boshlanish, $tugash)) {
                return $paraRaqam;
            }
        }

        return null;
    }

    /**
     * Bugungi kun uchun davomat olish mumkin bo'lgan paralar
     */
    public static function mavjudParalar(): array
    {
        $mavjud = [];

        foreach (self::PARALAR as $paraRaqam => $vaqtlar) {
            if (self::paraTugadimi($paraRaqam)) {
                $mavjud[] = $paraRaqam;
            }
        }

        return $mavjud;
    }

    /**
     * Berilgan para uchun davomat olish mumkinmi (vaqt bo'yicha)
     */
    public static function davomatOlishMumkinmi(int $para, ?string $sana = null): bool
    {
        // Agar sana bugun bo'lmasa, faqat admin o'zgartira oladi
        $bugun = self::bugungiSana();

        if ($sana && $sana !== $bugun) {
            return false; // O'tgan sanalar uchun davomat olish mumkin emas
        }

        return self::paraTugadimi($para);
    }

    /**
     * Para nomini olish
     */
    public static function paraNomi(int $para): string
    {
        return self::PARALAR[$para]['nomi'] ?? '-';
    }

    /**
     * Barcha paralarni olish (select uchun)
     */
    public static function barchaParalar(): array
    {
        $paralar = [];
        foreach (self::PARALAR as $raqam => $vaqtlar) {
            $paralar[$raqam] = $vaqtlar['nomi'];
        }
        return $paralar;
    }

    /**
     * Keyingi para qachon tugaydi (timer uchun)
     */
    public static function keyingiParaTugashVaqti(): ?Carbon
    {
        $hozir = self::hozirgiVaqt();
        $bugun = self::bugungiSana();

        foreach (self::PARALAR as $paraRaqam => $vaqtlar) {
            $tugash = Carbon::parse($bugun . ' ' . $vaqtlar['tugash'], self::TIMEZONE);

            if ($hozir->lessThan($tugash)) {
                return $tugash;
            }
        }

        return null;
    }

    /**
     * Debug uchun hozirgi holat
     */
    public static function holatInfo(): array
    {
        return [
            'hozirgi_vaqt' => self::hozirgiVaqt()->format('H:i:s'),
            'bugungi_sana' => self::bugungiSana(),
            'hozirgi_para' => self::hozirgiPara(),
            'davomat_para' => self::hozirgiDavomatPara(),
            'mavjud_paralar' => self::mavjudParalar(),
            'keyingi_tugash' => self::keyingiParaTugashVaqti()?->format('H:i:s'),
        ];
    }
}
