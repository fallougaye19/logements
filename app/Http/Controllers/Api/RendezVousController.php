<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rendez_vous;
use App\Http\Requests\StoreRendezVousRequest;
use App\Models\Chambre;
use Illuminate\Support\Facades\Auth; // Assuming you have a request class for validation

class RendezVousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $query = Rendez_vous::with('chambre');

        if ($user->role === 'proprietaire') {
            $query->whereHas('chambre.maison', fn($q) => $q->where('proprietaire_id', $user->id));
        } elseif ($user->role === 'locataire') {
            $query->where('locataire_id', $user->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $rdvs = $query->orderBy('date_heure', 'desc')->get();
        return response()->json(['success' => true, 'data' => $rdvs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRendezVousRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'chambre_id' => 'required|exists:chambres,id',
            'date_heure' => 'required|date|after:now',
        ]);

        $chambre = Chambre::find($validated['chambre_id']);

        // Vérifier que la chambre est disponible
        if (!$chambre->disponible) {
            return response()->json([
                'success' => false,
                'message' => 'La chambre n\'est plus disponible.'
            ], 400);
        }

        $rdv = Rendez_vous::create([
            'locataire_id' => $user->id,
            'chambre_id' => $validated['chambre_id'],
            'date_heure' => $validated['date_heure'],
            'statut' => 'en_attente',
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Rendez-vous pris avec succès.',
            'data' => $rdv->load('chambre')
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Rendez_vous $rendezVous)
    {
        return response()->json(['success' => true, 'data' => $rendezVous]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rendez_vous $rendezVous)
    {
        $validated = $request->validate([
            'date_heure' => 'required|date',
        ]);

        $rendezVous->update($validated);
        return response()->json(['success' => true, 'data' => $rendezVous]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rendez_vous $rendezVous)
    {
        $rendezVous->delete();
        return response()->noContent();
    }

    public function updateStatut(Request $request, Rendez_vous $rendez_vous)
    {
        $user = Auth::user();

        if ($user->role !== 'proprietaire') {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $validated = $request->validate([
            'statut' => 'required|in:en_attente,confirmé,annulé'
        ]);

        $rendez_vous->update(['statut' => $request->statut]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour.',
            'data' => $rendez_vous
        ]);
    }
}

