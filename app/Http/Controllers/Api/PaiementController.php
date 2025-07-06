<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Http\Requests\StorePaiementRequest; // Assuming you have a request class for validation

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Paiement::with(['contrat.locataire', 'contrat.chambre'])
            ->where('locataire_id', $request->user()->id)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaiementRequest $request)
    {
        $data = $request->validated();
        $data['locataire_id'] = $request->user()->id;
        $data['proprietaire_id'] = $request->user()->proprietaire_id; // à adapter selon ta logique

        $data['statut'] = 'en attente'; // Ou 'confirmé' si paiement instantané
        return Paiement::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        return $paiement;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $data = $request->validate([
            'statut' => 'sometimes|required|in: en attente,confirme,refuse',
            'date_paiement' => 'nullable|date'
        ]);

        $paiement->update($data);
        return $paiement;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return response()->noContent();
    }
}
