<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Maison>
 */
class MaisonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'adresse' => $this->faker->address(),
            'latitude' => $this->faker->latitude(14.6, 14.8),  // Dakar approx
            'longitude' => $this->faker->longitude(-17.6, -17.2),
            'description' => $this->faker->paragraph(),
            'proprietaire_id' => null,
        ];
    }
}
