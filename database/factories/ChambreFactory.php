<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Maison;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chambre>
 */
class ChambreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'taille' => $this->faker->randomElement(['10m²', '12m²', '15m²']),
            'type' => $this->faker->randomElement(['simple', 'appartement', 'studio']),
            'meublee' => $this->faker->boolean(70), // 70% oui
            'salle_de_bain' => $this->faker->boolean(60),
            'prix' => $this->faker->numberBetween(50000, 250000),
            'disponible' => $this->faker->boolean(90),
            'maison_id' => Maison::Factory(),
        ];
    }
}
