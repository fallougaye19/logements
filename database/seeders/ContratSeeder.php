<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Chambre;
use App\Models\User;
use App\Models\Contrat;

class ContratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locataires = User::where('role', 'locataire')->get();
        $chambresDisponibles = Chambre::where('disponible', true)->get()->shuffle();

        foreach ($locataires as $locataire) {
            // Vérifie qu'il reste des chambres disponibles
            if ($chambresDisponibles->isEmpty()) break;

            // Attribue une chambre
            $chambre = $chambresDisponibles->pop();

            Contrat::factory()->create([
                'locataire_id' => $locataire->id,
                'chambre_id' => $chambre->id,
            ]);

            // Marquer la chambre comme non disponible
            $chambre->update(['disponible' => false]);
        }
    }
}
