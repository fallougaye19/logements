<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chambre;
use App\Models\Maison;

class ChambreTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maisons = Maison::all();

        foreach ($maisons as $maison) {
            Chambre::create([
                'maison_id' => $maison->id,
                'titre' => 'Chambre test 1',
                'description' => 'Une chambre test confortable et lumineuse.',
                'taille' => '12m²',
                'type' => 'simple',
                'meublee' => true,
                'salle_de_bain' => true,
                'prix' => 75000,
                'disponible' => true,
            ]);

            Chambre::create([
                'maison_id' => $maison->id,
                'titre' => 'Chambre test 2',
                'description' => 'Chambre test avec vue sur le jardin.',
                'taille' => '15m²',
                'type' => 'appartement',
                'meublee' => false,
                'salle_de_bain' => false,
                'prix' => 65000,
                'disponible' => true,
            ]);
        }
    }
}
