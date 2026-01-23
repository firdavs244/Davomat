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
     * Para vaqtlari (boshlanish va tugash vaqti)
     * Davomat faqat para davomida olinishi mumkin
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
        4 => [
            'boshlanish' => '13:30',
            'tugash' => '14:50',
            'nomi' => '4-para (13:30 - 14:50)',
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
     * Davomat faqat para davomida (boshlanishidan tugashigacha) olinishi mumkin
     * 4-para tugagandan keyin ertangi 1-parani kutish kerak
     */
    public static function hozirgiDavomatPara(): ?int
    {
        $hozir = self::hozirgiVaqt();
        $bugun = self::bugungiSana();

        // Hozirgi vaqtda qaysi para davom etyapti
        foreach (self::PARALAR as $paraRaqam => $vaqtlar) {
            $boshlanish = Carbon::parse($bugun . ' ' . $vaqtlar['boshlanish'], self::TIMEZONE);
            $tugash = Carbon::parse($bugun . ' ' . $vaqtlar['tugash'], self::TIMEZONE);

            // Agar hozirgi vaqt bu para vaqtida bo'lsa
            if ($hozir->between($boshlanish, $tugash)) {
                return $paraRaqam;
            }
        }

        return null; // Hech qanday para vaqtida emas
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
     * Bugungi kun uchun davomat olish mumkin bo'lgan paralar (hozir davom etyapti)
     */
    public static function mavjudParalar(): array
    {
        $hozirgiPara = self::hozirgiDavomatPara();

        if ($hozirgiPara !== null) {
            return [$hozirgiPara];
        }

        return [];
    }

    /**
     * Berilgan para uchun davomat olish mumkinmi (vaqt bo'yicha)
     * Davomat faqat para davomida olinishi mumkin
     */
    public static function davomatOlishMumkinmi(int $para, ?string $sana = null): bool
    {
        // Agar sana bugun bo'lmasa, faqat admin o'zgartira oladi
        $bugun = self::bugungiSana();

        if ($sana && $sana !== $bugun) {
            return false; // O'tgan sanalar uchun davomat olish mumkin emas
        }

        // Hozirgi para bilan mos kelishi kerak
        return self::hozirgiDavomatPara() === $para;
    }

    /**
     * 4-para tugaganmi va ertangi kun kutilmoqdami
     */
    public static function kunTugadimi(): bool
    {
        $hozir = self::hozirgiVaqt();
        $bugun = self::bugungiSana();

        // 4-para tugash vaqti
        $oxirgiParaTugash = Carbon::parse($bugun . ' ' . self::PARALAR[4]['tugash'], self::TIMEZONE);

        return $hozir->greaterThan($oxirgiParaTugash);
    }

    /**
     * Keyingi para qachon boshlanadi (timer uchun)
     */
    public static function keyingiParaBoshlanishVaqti(): ?Carbon
    {
        $hozir = self::hozirgiVaqt();
        $bugun = self::bugungiSana();

        // Agar kun tugagan bo'lsa (4-para tugagandan keyin), ertangi 1-para
        if (self::kunTugadimi()) {
            $ertaga = Carbon::parse($bugun, self::TIMEZONE)->addDay();
            return Carbon::parse($ertaga->toDateString() . ' ' . self::PARALAR[1]['boshlanish'], self::TIMEZONE);
        }

        // Keyingi boshlanadigan parani topish
        foreach (self::PARALAR as $paraRaqam => $vaqtlar) {
            $boshlanish = Carbon::parse($bugun . ' ' . $vaqtlar['boshlanish'], self::TIMEZONE);

            if ($hozir->lessThan($boshlanish)) {
                return $boshlanish;
            }
        }

        return null;
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
        $hozirgiPara = self::hozirgiDavomatPara();

        if ($hozirgiPara === null) {
            return null;
        }

        $bugun = self::bugungiSana();
        return Carbon::parse($bugun . ' ' . self::PARALAR[$hozirgiPara]['tugash'], self::TIMEZONE);
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
            'keyingi_boshlanish' => self::keyingiParaBoshlanishVaqti()?->format('H:i:s'),
            'keyingi_tugash' => self::keyingiParaTugashVaqti()?->format('H:i:s'),
            'kun_tugadi' => self::kunTugadimi(),
        ];
    }
}
