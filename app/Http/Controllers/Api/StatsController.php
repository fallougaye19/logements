<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Maison;
use App\Models\Chambre;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Probleme;
use App\Models\Rendez_vous;

class StatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function stats()
    {
        try {
            return response()->json([
                'utilisateurs' => User::count(),
                'maisons' => Maison::count(),
                'chambres' => Chambre::count(),
                "contrats_actifs" => Contrat::where('actif', true)->count(),
                "rendez_vous_a_venir" => Rendez_vous::where("date_heure", ">", now())->count(),
                "problemes_non_resolus" => Probleme::where("resolu", false)->count(),
                "paiements_en_retard" => Paiement::where("statut", "en attente")->count()
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
