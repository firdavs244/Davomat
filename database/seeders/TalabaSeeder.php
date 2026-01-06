<?php

namespace Database\Seeders;

use App\Models\Guruh;
use App\Models\Talaba;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TalabaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ismlar = [
            'Abdullayev Sardor', 'Axmedov Bobur', 'Aliyev Jasur', 'Azimov Ulug\'bek',
            'Bekmurodov Shahzod', 'Bobojonov Azizbek', 'Davlatov Dilshod', 'Ergashev Komil',
            'Eshonov Shoxrux', 'Fayzullayev Murod', 'G\'aniyev Bekzod', 'Habibullayev Xurshid',
            'Husanov Jahongir', 'Ibragimov Sanjarbek', 'Ismoilov Umarali', 'Jalolov Alijon',
            'Jurayev Umid', 'Karimov Javohir', 'Komilov Otabek', 'Latipov Nuriddin',
            'Mahmudov Dilshodbek', 'Mirzayev Firdavs', 'Nabiyev Abdulla', 'Nazarov Sardor',
            'Normatov Sherzod', 'Olimov Temur', 'Ortiqov Botir', 'Qodirov Dilmurod',
            'Rahimov Shokir', 'Raxmonov Rustam', 'Saidov Abror', 'Salimov Anvar',
            'Sharipov Dostonbek', 'Toshmatov Sunnat', 'Tursunov Lochinbek', 'Umarov Marufjon',
            'Usmonov Nodir', 'Xaydarov Doniyor', 'Xolmatov Sirojiddin', 'Yuldashev Azamat',
            'Yusupov Bekjon', 'Zaripov Mirjalol', 'Zokirov Muhammadali', 'Qobulova Malika',
            'Karimova Dilorom', 'Aliyeva Madina', 'Rahmonova Sarvinoz', 'Ismoilova Dilnoza',
            'Sodiqova Marjona', 'Tursunova Gulnoza', 'Azimova Shahzoda', 'Bekmurodova Zarina',
        ];

        $guruhlar = Guruh::all();
        $ismlarIndex = 0;

        foreach ($guruhlar as $guruh) {
            // 20-30 talaba har bir guruh uchun
            $talabaSoni = rand(20, 25);
            
            for ($i = 0; $i < $talabaSoni; $i++) {
                // Kirgan sana - o'tgan yil sentyabrdan oldin
                $kirganSana = Carbon::create(now()->year - ($guruh->kurs - 1), 9, 1)->startOfMonth();
                
                // Ba'zi talabalar keyinroq kelgan (10%)
                if (rand(1, 100) <= 10) {
                    $kirganSana = $kirganSana->addMonths(rand(1, 3));
                }
                
                // Ba'zi talabalar ketgan (5%)
                $ketganSana = null;
                $holati = 'aktiv';
                if (rand(1, 100) <= 5) {
                    $ketganSana = Carbon::now()->subDays(rand(10, 60));
                    $holati = 'ketgan';
                }
                
                // Ba'zi talabalar akademik ta'tilda (3%)
                if ($holati == 'aktiv' && rand(1, 100) <= 3) {
                    $holati = 'akademik_tatil';
                }
                
                Talaba::create([
                    'guruh_id' => $guruh->id,
                    'fish' => $ismlar[$ismlarIndex % count($ismlar)] . ' ' . chr(65 + ($ismlarIndex / count($ismlar))),
                    'kirgan_sana' => $kirganSana->format('Y-m-d'),
                    'ketgan_sana' => $ketganSana?->format('Y-m-d'),
                    'holati' => $holati,
                ]);
                
                $ismlarIndex++;
            }
        }
    }
}
