<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nom' => 'Admin Test',
            'email' => 'admin@test.com',
            'telephone'=> '777778899',
            'cni' => '2345678909887',
            'password' => Hash::make('password'),
            'role' => 'proprietaire',
        ]);

        // Locataires
        User::factory(10)->create(['role' => 'locataire']);
    }
}
