<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guruh>
 */
class GuruhFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomi' => 'AT-' . fake()->numberBetween(101, 299),
            'kurs' => fake()->numberBetween(1, 2), // Kollejda faqat 1 va 2-kurs
            'yunalish' => fake()->randomElement([
                'Axborot texnologiyalari',
                'Menejment',
                'Hisobchilik',
                'Elektrotexnika',
            ]),
            'is_active' => true,
        ];
    }
}
