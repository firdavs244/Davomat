<?php

namespace Database\Seeders;

use App\Models\Guruh;
use Illuminate\Database\Seeder;

class GuruhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guruhlar = [
            [
                'nomi' => 'AT-101',
                'kurs' => '1',
                'yunalish' => 'Axborot texnologiyalari',
                'is_active' => true,
            ],
            [
                'nomi' => 'AT-201',
                'kurs' => '2',
                'yunalish' => 'Axborot texnologiyalari',
                'is_active' => true,
            ],
            [
                'nomi' => 'M-102',
                'kurs' => '1',
                'yunalish' => 'Menejment',
                'is_active' => true,
            ],
            [
                'nomi' => 'H-301',
                'kurs' => '3',
                'yunalish' => 'Hisobchilik',
                'is_active' => true,
            ],
            [
                'nomi' => 'E-203',
                'kurs' => '2',
                'yunalish' => 'Elektrotexnika',
                'is_active' => true,
            ],
        ];

        foreach ($guruhlar as $guruh) {
            Guruh::create($guruh);
        }
    }
}
