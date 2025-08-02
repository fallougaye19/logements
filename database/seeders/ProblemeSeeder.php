<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contrat;
use App\Models\User;
use App\Models\Probleme;

class ProblemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contrats = Contrat::all();
        $utilisateurs = User::all();

        foreach ($contrats as $contrat) {
            // Générer 0 à 2 problèmes par contrat
            $count = rand(0, 2);

            for ($i = 0; $i < $count; $i++) {
                // Choisir un utilisateur au hasard (propriétaire ou locataire)
                $signalant = $utilisateurs->random();

                Probleme::factory()->create([
                    'contrat_id' => $contrat->id,
                    'signale_par' => $signalant->id,
                ]);
            }
        }
    }
}
