<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Chambre;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ContratFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $chambre = Chambre::factory()->create();

        return [
            'locataire_id' => User::factory()->locataire(),
            'chambre_id' => $chambre->id,
            'proprietaire_id' => $chambre->maison->proprietaire_id,
            'date_debut' => now()->subDays(rand(0, 30)),
            'date_fin' => now()->addYears(1),
            'montant_caution' => $chambre->prix * $this->faker->numberBetween(1, 3),
            'mois_caution' => $this->faker->numberBetween(1, 3),
            'description' => "Contrat standard de location.",
            'mode_paiement' => $this->faker->randomElement(['virement', 'espèces', 'mobile_money']),
            'periodicite' => 'mensuel',
            'statut' => 'actif',
        ];
    }
}
