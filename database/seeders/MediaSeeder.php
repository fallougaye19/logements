<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chambre;
use App\Models\Media;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chambres = Chambre::all();

        foreach ($chambres as $chambre) {
            // Chaque chambre a entre 1 et 4 médias (photos/vidéos)
            Media::factory(rand(1, 4))->create([
                'chambre_id' => $chambre->id,
            ]);
        }
    }
}
