<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Chambre;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class RendezVousFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\Rendez_vous::class;

    public function definition()
    {
        return [
            'locataire_id' => User::factory()->locataire(),
            'chambre_id' => Chambre::factory(),
            'date_heure' => $this->faker->dateTimeBetween('+1 day', '+2 weeks'),
            'statut' => $this->faker->randomElement(['en_attente', 'confirmé', 'annulé']),
        ];
    }
}
