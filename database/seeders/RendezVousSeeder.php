<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chambre;
use App\Models\Rendez_vous;

class RendezVousSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locataires = User::where('role', 'locataire')->get();
        $chambres = Chambre::where('disponible', true)->get()->shuffle();

        foreach ($locataires as $locataire) {
            if ($chambres->isEmpty()) break;

            $chambre = $chambres->pop();

            // 1 à 2 rendez-vous par locataire
            Rendez_vous::factory(rand(1, 2))->create([
                'locataire_id' => $locataire->id,
                'chambre_id' => $chambre->id,
            ]);
        }
    }
}
