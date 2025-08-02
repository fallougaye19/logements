<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Maison;

class MaisonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proprietaires = User::where('role', 'proprietaire')->get();

        foreach ($proprietaires as $proprio) {
            // Chaque propriétaire aura entre 1 et 3 maisons
            Maison::factory(rand(1, 3))->create([
                'proprietaire_id' => $proprio->id,
            ]);
        }
    }
}
