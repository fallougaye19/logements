<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Chambre;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chambre_id' => Chambre::factory(),
            'locataire_id' => User::factory()->locataire(),
            'message' => $this->faker->optional()->paragraph(),
            'statut' => $this->faker->randomElement(['en_attente', 'acceptee', 'refusee']),
        ];
    }
}
