<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contrat;
use App\Http\Requests\StoreContratRequest; // Assuming you have a request class for validation

class ContratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Contrat::with(['locataire','chambre'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContratRequest $request)
    {
        $data = $request->validated();
        return Contrat::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contrat $contrat)
    {
        return $contrat->load('locataire','chambre','paiements', 'problemes');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contrat $contrat)
    {
        $data = $request->validate([
            'locataire_id' => 'sometimes|required|exists:users,id',
            'chambre_id' => 'sometimes|required|exists:chambres,id',
            'date_debut' => 'sometimes|required|date',
            'date_fin' => 'sometimes|required|date|after:date_debut',
            'montant_caution' => 'sometimes|required|numeric',
            'mois_caution' => 'sometimes|required|integer|min:1',
            'description' => 'nullable|string',
            'mode_paiement' => 'sometimes|required|string',
            'periodicite' => 'sometimes|required|string',
            'statut' => 'sometimes|required|string'
        ]);

        $contrat->update($data);
        return $contrat;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contrat $contrat)
    {
        $contrat->delete();
        return response()->noContent();
    }
}
