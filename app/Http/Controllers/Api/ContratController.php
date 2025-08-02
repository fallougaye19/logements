<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contrat;
use App\Http\Requests\StoreContratRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Chambre;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Assuming you have a request class for validation

class ContratController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $query = Contrat::with([
            'proprietaire:id,nom,email,telephone',
            'locataire:id,nom,email,telephone',
            'chambre',
        ]);

        if ($user->role === 'proprietaire') {
            $query->where('proprietaire_id', $user->id);
        } elseif ($user->role === 'locataire') {
            $query->where('locataire_id', $user->id);
        } elseif ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $contrats = $query->get();

        return response()->json(['success' => true, 'data' => $contrats]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContratRequest $request)
    {
        $user = Auth::user();

        if ($user->role !== 'proprietaire') {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }
        $data = $request->validated();
        $chambre = Chambre::find($data['chambre_id']);

        if ($chambre->maison->proprietaire_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne gérez pas cette maison.'
            ], 403);
        }

        if (!$chambre->disponible) {
            return response()->json([
                'success' => false,
                'message' => 'Cette chambre n\'est pas disponible.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $contrat = Contrat::create([
                'locataire_id' => $data['locataire_id'],
                'chambre_id' => $data['chambre_id'],
                'proprietaire_id' => $user->id,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'montant_caution' => $data['montant_caution'],
                'mois_caution' => $data['mois_caution'],
                'description' => $data['description'] ?? '',
                'mode_paiement' => $data['mode_paiement'],
                'periodicite' => $data['periodicite'],
                'statut' => 'actif',
            ]);

            // Générer les paiements futurs (ex: 12 mois)
            $nbMois = 12;
            $montant = $chambre->prix;
            for ($i = 1; $i <= $nbMois; $i++) {
                Paiement::create([
                    'contrat_id' => $contrat->id,
                    'montant' => $montant,
                    'statut' => 'impayé',
                    'date_echeance' => now()->addMonths($i),
                ]);
            } // Marquer la chambre comme non disponible
            $chambre->update(['disponible' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrat créé avec succès.',
                'data' => $contrat->load(['locataire', 'chambre'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contrat $contrat)
    {
        // Récupère l'utilisateur authentifié
        $user = Auth::user();
        Log::info("User: " . $user->id . ", Role: " . $user->role);
        Log::info("Contrat: " . $contrat->id . ", Proprietaire: " . $contrat->proprietaire_id . ", Locataire: " . $contrat->locataire_id);

        // Vérifie les permissions
        // On refuse l'accès si l'utilisateur n'est ni le propriétaire, ni le locataire du contrat.
        $isProprietaire = $user->role === 'proprietaire' && $contrat->proprietaire_id === $user->id;
        $isLocataire = $user->role === 'locataire' && $contrat->locataire_id === $user->id;

        if (!$isProprietaire && !$isLocataire) {
            // Si l'utilisateur n'a aucun des deux rôles autorisés, on refuse l'accès.
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        // Charge toutes les relations nécessaires en une seule fois
        // avec les colonnes spécifiques pour optimiser la requête.
        $contrat->load([
            'locataire:id,nom,email,telephone',
            'proprietaire:id,nom,email,telephone',
            'chambre:id,titre,maison_id,prix',
            'chambre.maison:id,adresse',
            'paiements',
            'problemes'
        ]);

        // Retourne une réponse de succès avec les données du contrat
        return response()->json([
            'success' => true,
            'data' => $contrat
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreContratRequest $request, Contrat $contrat)
    {
        $user = Auth::user();

        if ($contrat->proprietaire_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        if ($contrat->statut === 'resilie') {
            return response()->json(['success' => false, 'message' => 'Impossible de modifier un contrat résilié.'], 400);
        }

        $data = $request->validated();
        $contrat->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Contrat mis à jour.',
            'data' => $contrat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contrat $contrat)
    {
        $user = Auth::user();

        if ($contrat->proprietaire_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        if ($contrat->statut !== 'actif') {
            return response()->json(['success' => false, 'message' => 'Impossible de supprimer un contrat non actif.'], 400);
        }

        DB::beginTransaction();
        try {
            // 🔹 Charger la chambre
            $contrat->load('chambre');
            // Marquer la chambre comme disponible
            $contrat->chambre->update(['disponible' => true]);

            // Supprimer les paiements
            $contrat->paiements()->delete();

            // Supprimer le contrat
            $contrat->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrat supprimé avec succès.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resilier(Request $request, Contrat $contrat)
    {
        $user = Auth::user();

        if ($contrat->proprietaire_id !== $user->id && $contrat->locataire_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        if ($contrat->statut !== 'actif') {
            return response()->json(['success' => false, 'message' => 'Le contrat n\'est pas actif.'], 400);
        }

        DB::beginTransaction();
        try {
            $contrat->update(['statut' => 'resilie']);

            $contrat->load('chambre');

            // Optionnel : libérer la chambre
            $contrat->chambre->update(['disponible' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contrat résilié avec succès.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la résiliation.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
