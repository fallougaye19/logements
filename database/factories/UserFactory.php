<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // password
            'role' => $this->faker->randomElement(['proprietaire', 'locataire']),
            'telephone' => $this->faker->phoneNumber(),
            'cni' => $this->faker->unique()->numerify('###########'),
        ];
    }

    // État : propriétaire
    public function proprietaire()
    {
        return $this->state(['role' => 'proprietaire']);
    }

    // État : locataire
    public function locataire()
    {
        return $this->state(['role' => 'locataire']);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
