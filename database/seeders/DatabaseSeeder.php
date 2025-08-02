<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Maison;
use App\Models\Chambre;
use App\Models\Reservation;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Probleme;
use App\Models\Rendez_vous;
use App\Models\Media;
use Database\Factories\MediaFactory;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Créer 2 propriétaires
        $proprietaires = User::factory()->proprietaire()->count(2)->create();

        // Créer 3 locataires
        $locataires = User::factory()->locataire()->count(3)->create();

        // Chaque propriétaire a 1 maison
        foreach ($proprietaires as $proprietaire) {
            $maison = Maison::factory()->create(['proprietaire_id' => $proprietaire->id]);

            // 2 à 4 chambres par maison
            $chambres = Chambre::factory()->count(rand(2, 4))->create(['maison_id' => $maison->id]);

            // Ajouter des images
            foreach ($chambres as $chambre) {
                Media::Factory()->count(2)->create(['chambre_id' => $chambre->id]);
            }

            // 50% de chance d'avoir un contrat
            if (rand(0, 1)) {
                $locataire = $locataires->random();
                $chambre = $chambres->random();
                $contrat = Contrat::factory()->create([
                    'locataire_id' => $locataire->id,
                    'chambre_id' => $chambre->id,
                    'proprietaire_id' => $proprietaire->id
                ]);

                // 6 paiements
                Paiement::factory()->count(6)->create(['contrat_id' => $contrat->id]);

                // 30% de chance d'avoir un problème
                if (rand(0, 9) < 3) {
                    Probleme::factory()->create(['contrat_id' => $contrat->id]);
                }
            }

            // 2 rendez-vous par maison
            Rendez_vous::factory()->count(2)->create(['chambre_id' => $chambres->first()->id]);
        }

        // Réservations en attente
        Reservation::factory()->count(3)->create(['statut' => 'en_attente']);
    }
}
