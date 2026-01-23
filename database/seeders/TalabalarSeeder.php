<?php

namespace Database\Seeders;

use App\Models\Guruh;
use App\Models\Talaba;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TalabalarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ma'lumotlarni alohida fayldan yuklash
        $talabalar = require __DIR__ . '/data/talabalar_data.php';

        // Avval guruhlarni yaratish yoki olish
        $guruhlarCache = [];

        foreach ($talabalar as $data) {
            // O'quv yilidan kursni aniqlash
            $kurs = $this->aniqlaKurs($data['oquv_yili']);

            // Guruhni olish yoki yaratish
            $guruhNomi = $data['guruh'];

            if (!isset($guruhlarCache[$guruhNomi])) {
                $guruh = Guruh::firstOrCreate(
                    ['nomi' => $guruhNomi],
                    [
                        'kurs' => $kurs,
                        'yunalish' => $data['yunalish'],
                        'is_active' => true,
                    ]
                );
                $guruhlarCache[$guruhNomi] = $guruh->id;
            }

            // Tug'ilgan sanani parse qilish
            $tugilganSana = null;
            if (!empty($data['tugilgan_sana'])) {
                try {
                    $tugilganSana = Carbon::createFromFormat('d.m.Y', $data['tugilgan_sana']);
                } catch (\Exception $e) {
                    $tugilganSana = null;
                }
            }

            // Pasportni tozalash
            $pasport = !empty($data['pasport']) ? trim($data['pasport']) : null;
            if ($pasport === '' || $pasport === "'" || $pasport === '-') {
                $pasport = null;
            }

            // Talabani yaratish
            Talaba::create([
                'fish' => $data['fish'],
                'jshshir' => $data['jshshir'],
                'pasport' => $pasport,
                'tugilgan_sana' => $tugilganSana,
                'jinsi' => $data['jinsi'],
                'qabul_turi' => $data['qabul_turi'],
                'talim_shakli' => $data['talim_shakli'],
                'oquv_yili' => $data['oquv_yili'],
                'tuman' => $data['tuman'],
                'manzil' => $data['manzil'],
                'guruh_id' => $guruhlarCache[$guruhNomi],
                'kirgan_sana' => Carbon::create(2025, 9, 1), // O'quv yili boshlanishi
                'ketgan_sana' => null,
                'holati' => 'aktiv',
            ]);
        }

        $this->command->info('✅ ' . count($talabalar) . ' ta talaba muvaffaqiyatli yuklandi!');
    }

    /**
     * O'quv yilidan kursni aniqlash
     *
     * Formula: kurs = (jami yillar) - (qolgan yillar)
     *
     * Misol (hozir 2026):
     * - 2025-2027: jami=2, qolgan=1, kurs=2-1=1 ✓
     * - 2024-2026: jami=2, qolgan=0, kurs=2-0=2 ✓
     * - 2023-2025: jami=2, qolgan=-1, kurs=2-(-1)=3 ✓
     * - 2022-2024: jami=2, qolgan=-2, kurs=2-(-2)=4 ✓
     *
     * @param string|null $oquvYili - Masalan: "2025-2027", "2024-2026"
     * @return int - Kurs raqami (1, 2, 3, 4)
     */
    private function aniqlaKurs(?string $oquvYili): int
    {
        if (empty($oquvYili)) {
            return 1;
        }

        $yillar = explode('-', $oquvYili);

        if (count($yillar) !== 2) {
            return 1;
        }

        $boshlanishYili = (int) trim($yillar[0]);
        $tugashYili = (int) trim($yillar[1]);
        $hozirgiYil = (int) date('Y');

        // Jami ta'lim muddati (yillar)
        $jamiYillar = $tugashYili - $boshlanishYili;

        // Qolgan yillar
        $qolganYillar = $tugashYili - $hozirgiYil;

        // O'tgan yillar = Kurs
        $kurs = $jamiYillar - $qolganYillar;

        // Agar hali boshlanmagan bo'lsa
        if ($kurs <= 0) {
            return 1;
        }

        // Maksimum 4-kurs
        if ($kurs > 4) {
            return 4;
        }

        return $kurs;
    }
}
