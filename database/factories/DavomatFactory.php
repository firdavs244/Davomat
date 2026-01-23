<?php

namespace Database\Factories;

use App\Models\Guruh;
use App\Models\Talaba;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Davomat>
 */
class DavomatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'talaba_id' => Talaba::factory(),
            'guruh_id' => Guruh::factory(),
            'sana' => Carbon::now()->subDays(fake()->numberBetween(0, 30))->format('Y-m-d'),
            'para_1' => fake()->randomElement(['bor', 'yoq']),
            'para_2' => fake()->randomElement(['bor', 'yoq']),
            'para_3' => fake()->randomElement(['bor', 'yoq', null]),
            'para_4' => fake()->randomElement(['bor', 'yoq', null]),
            'xodim_id' => User::factory(),
        ];
    }
}
