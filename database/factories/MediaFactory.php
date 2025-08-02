<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Chambre;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Génère un nom de fichier aléatoire
        $filename = 'chambres/' . $this->faker->uuid() . '.jpg';
        Storage::disk('public')->put($filename, $this->faker->image(null, 800, 600));

        return [
            'chambre_id' => Chambre::factory(),
            'path' => $filename,
            'url' => asset('storage/' . $filename),
        ];
    }
}
