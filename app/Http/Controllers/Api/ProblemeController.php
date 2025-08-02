<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Probleme;
use App\Http\Requests\StoreProblemeRequest;
use App\Models\Contrat;
use Illuminate\Support\Facades\Auth;

class ProblemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Probleme::with(['contrat.chambre', 'auteur']);

        if ($user->role === 'proprietaire') {
            $query->whereHas('contrat', fn($q) => $q->where('proprietaire_id', $user->id));
        } elseif ($user->role === 'locataire') {
            $query->whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id));
        }

        $problemes = $query->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $problemes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProblemeRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'contrat_id' => 'required|exists:contrats,id',
            'description' => 'required|string',
            'type' => 'required|in:plomberie,electricite,serrurerie,autre',
            'responsable' => 'nullable|in:locataire,proprietaire,indetermine',
        ]);

        $contrat = Contrat::find($validated['contrat_id']);

        // Vérifier que l'utilisateur est lié au contrat
        if ($contrat->locataire_id !== $user->id && $contrat->proprietaire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $probleme = Probleme::create([
            'contrat_id' => $request->contrat_id,
            'signale_par' => $user->id,
            'description' => $request->description,
            'type' => $request->type,
            'responsable' => $request->responsable ?? null,
            'resolu' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Problème signalé.',
            'data' => $probleme->load('auteur')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Probleme $probleme)
    {
        $user = Auth::user();

        if ($user->role === 'proprietaire' && $probleme->contrat->proprietaire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        if ($user->role === 'locataire' && $probleme->contrat->locataire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $probleme->load('auteur', 'contrat.chambre.maison')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Probleme $probleme)
    {
        $user = Auth::user();

        if ($user->role === 'proprietaire' && $probleme->contrat->proprietaire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $request->validate([
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:plomberie,electricite,serrurerie,autre',
            'responsable' => 'nullable|in:locataire,proprietaire,indetermine',
            'resolu' => 'boolean'
        ]);

        $probleme->update($request->only(['description', 'type', 'responsable', 'resolu']));

        return response()->json([
            'success' => true,
            'message' => 'Problème mis à jour.',
            'data' => $probleme
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Probleme $probleme)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $probleme->contrat->proprietaire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $probleme->delete();

        return response()->json([
            'success' => true,
            'message' => 'Problème supprimé.'
        ]);
    }

    public function resoudre(Probleme $probleme)
    {
        $user = Auth::user();

        if ($probleme->contrat->proprietaire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Seul le propriétaire peut marquer comme résolu.'], 403);
        }

        $probleme->update(['resolu' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Problème marqué comme résolu.',
            'data' => $probleme->load('auteur', 'contrat.chambre')
        ]);
    }
}
