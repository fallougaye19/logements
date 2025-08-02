<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contrat;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
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
            'montant' => $contrat->chambre->prix,
            'statut' => $this->faker->randomElement(['impayé', 'payé', 'en_retard']),
            'date_echeance' => $this->faker->dateTimeBetween('now', '+1 year'),
            'date_paiement' => function (array $attributes) {
                return $attributes['statut'] === 'payé' ? $this->faker->dateTimeBetween('-1 month', 'now') : null;
            },
        ];
    }
}
