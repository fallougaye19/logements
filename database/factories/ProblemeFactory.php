<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contrat;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Probleme>
 */
class ProblemeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contrat = Contrat::factory()->create();

        return [
            'contrat_id' => $contrat->id,
            'signale_par' => $contrat->locataire_id,
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['plomberie', 'electricite', 'serrurerie', 'autre']),
            'responsable' => $this->faker->optional()->randomElement(['locataire', 'proprietaire']),
            'resolu' => $this->faker->boolean(30), // 30% de chance d'être résolu
        ];
    }
}
