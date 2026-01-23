<?php

namespace Database\Factories;

use App\Models\Guruh;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Talaba>
 */
class TalabaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jinsi = fake()->randomElement(['Erkak', 'Ayol']);
        $tugilganSana = fake()->dateTimeBetween('-30 years', '-17 years');

        // JSHSHIR generatsiya qilish (14 raqam)
        $jshshir = str_pad((string) fake()->numerify('##############'), 14, '0', STR_PAD_LEFT);

        // Pasport generatsiya qilish (seriya + raqam) - ba'zilarda null
        $pasport = fake()->boolean(80)
            ? strtoupper(fake()->lexify('??')) . fake()->numerify('#######')
            : null;

        return [
            'fish' => fake()->lastName() . ' ' . fake()->firstName() . ' ' .
                ($jinsi === 'Erkak' ? fake()->firstName() . ' o\'g\'li' : fake()->firstName() . ' qizi'),
            'jshshir' => $jshshir,
            'pasport' => $pasport,
            'tugilgan_sana' => $tugilganSana->format('Y-m-d'),
            'jinsi' => $jinsi,
            'qabul_turi' => fake()->randomElement(['Grant', 'Kontrakt']),
            'talim_shakli' => fake()->randomElement(['Kunduzgi', 'Sirtqi', 'Kechki']),
            'oquv_yili' => '2025-2027',
            'tuman' => fake()->randomElement([
                'Buxoro shahri',
                'Buxoro tumani',
                'G\'ijduvon tumani',
                'Kogon shahri',
                'Qorako\'l tumani',
                'Romitan tumani',
                'Shofirkon tumani',
                'Vobkent tumani',
                'Jondor tumani',
                'Peshku tumani',
                'Olot tumani',
                'Qoravulbozor tumani',
            ]),
            'manzil' => fake()->address(),
            'guruh_id' => Guruh::factory(),
            'kirgan_sana' => Carbon::now()->subMonths(fake()->numberBetween(1, 12))->format('Y-m-d'),
            'ketgan_sana' => null,
            'holati' => 'aktiv',
        ];
    }
}
