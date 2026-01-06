<?php

namespace Database\Seeders;

use App\Models\Davomat;
use App\Models\Guruh;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DavomatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $xodimlar = User::whereIn('role', ['admin', 'davomat_oluvchi'])->get();
        $guruhlar = Guruh::with('aktivTalabalar')->get();
        
        // Oxirgi 30 kun uchun davomat yaratish (dam olish kunlaridan tashqari)
        $bugun = Carbon::today();
        $boshlanish = Carbon::today()->subDays(30);
        
        for ($sana = $boshlanish->copy(); $sana->lte($bugun); $sana->addDay()) {
            // Yakshanba va shanba kunlari o'tkazib yuborish
            if ($sana->dayOfWeek == Carbon::SUNDAY) {
                continue;
            }
            
            // Shanba kuni faqat 2 para
            $paraSoni = $sana->dayOfWeek == Carbon::SATURDAY ? 2 : 3;
            
            foreach ($guruhlar as $guruh) {
                foreach ($guruh->aktivTalabalar as $talaba) {
                    // Talaba kollej talabasi bo'lgan vaqtda davomatni yaratish
                    if (!$talaba->isKollejTalabasi($sana->format('Y-m-d'))) {
                        continue;
                    }
                    
                    // 85% davomat ko'rsatkichi
                    $para_1 = rand(1, 100) <= 88 ? 'bor' : 'yoq';
                    $para_2 = rand(1, 100) <= 85 ? 'bor' : 'yoq';
                    $para_3 = ($paraSoni >= 3) ? (rand(1, 100) <= 82 ? 'bor' : 'yoq') : null;
                    
                    // Ba'zi talabalar doim kech qoladi
                    if ($talaba->id % 5 == 0) {
                        $para_1 = rand(1, 100) <= 60 ? 'bor' : 'yoq';
                    }
                    
                    Davomat::create([
                        'talaba_id' => $talaba->id,
                        'guruh_id' => $guruh->id,
                        'sana' => $sana->format('Y-m-d'),
                        'para_1' => $para_1,
                        'para_2' => $para_2,
                        'para_3' => $para_3,
                        'xodim_id' => $xodimlar->random()->id,
                    ]);
                }
            }
        }
    }
}
