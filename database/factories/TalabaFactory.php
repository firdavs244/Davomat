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
        return [
            'fish' => fake()->lastName() . ' ' . fake()->firstName() . ' ' . fake()->firstName() . ' o\'g\'li',
            'guruh_id' => Guruh::factory(),
            'kirgan_sana' => Carbon::now()->subMonths(fake()->numberBetween(1, 12))->format('Y-m-d'),
            'ketgan_sana' => null,
            'holati' => 'aktiv',
        ];
    }
}
