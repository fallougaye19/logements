<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contrat;
use App\Models\Paiement;
use Faker\Factory as Faker;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $contrats = Contrat::all();

        foreach ($contrats as $contrat) {
            // On sécurise les dates
            $start = new \DateTime($contrat->date_debut);
            $end = new \DateTime($contrat->date_fin);

            if ($start > $end) {
                // on inverse au besoin pour éviter l’erreur
                [$start, $end] = [$end, $start];
            }

            // Génère entre 1 et 5 paiements par contrat
            $nbPaiements = rand(1, 5);

            for ($i = 0; $i < $nbPaiements; $i++) {
                Paiement::create([
                    'contrat_id' => $contrat->id,
                    'montant' => $faker->randomFloat(2, 20000, 150000),
                    'statut' => $faker->randomElement(['payé', 'impayé']),
                    'date_echeance' => $faker->dateTimeBetween($start, $end)->format('Y-m-d'),
                    'date_paiement' => $faker->dateTimeBetween($start, $end),
                    'cree_le' => now(),
                ]);
            }
        }
    }
}
