<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maison;
use App\Models\Chambre;

class ChambreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maisons = Maison::all();

        foreach ($maisons as $maison) {
            // Chaque maison aura entre 2 et 5 chambres
            Chambre::factory(rand(2, 5))->create([
                'maison_id' => $maison->id,
            ]);
        }
    }
}
