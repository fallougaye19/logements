<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Maison;
use App\Http\Requests\StoreMaisonRequest;
use App\Http\Requests\UpdateMaisonRequest;

class MaisonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Utiliser withCount pour éviter N+1 queries
        $query = Maison::with(['proprietaire:id,nom,email'])
            ->withCount('chambres');

        // Si l'utilisateur connecté n'est pas admin, filtrer ses propres maisons
        if (!$this->isAdmin()) {
            $query->byProprietaire(Auth::id());
        }

        // Filtres optionnels
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('adresse', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $maisons = $query->orderBy('id', 'desc')->get();

        // Optimisation: utiliser chambres_count au lieu de charger toutes les chambres
        $maisons->each(function ($maison) {
            // Utiliser setAttribute pour définir des propriétés virtuelles
            $maison->setAttribute('nombre_chambres', $maison->chambres_count);
            $maison->setAttribute('image_url', $maison->image_url);
        });

        return response()->json([
            'success' => true,
            'data' => $maisons,
            'total' => $maisons->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMaisonRequest $request)
    {
        try {
            $data = $request->validated();

            // Si pas de propriétaire spécifié, utiliser l'utilisateur connecté
            if (!isset($data['proprietaire_id'])) {
                $data['proprietaire_id'] = Auth::id();
            }

            // Vérification d'autorisation pour assigner un autre propriétaire
            if (isset($data['proprietaire_id']) && $data['proprietaire_id'] !== Auth::id() && !$this->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas assigner une maison à un autre propriétaire.'
                ], 403);
            }

            // Gestion de l'upload d'image
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('maisons', 'public');
                $data['image'] = $imagePath;
            }

            $maison = Maison::create($data);
            $maison->load(['proprietaire:id,nom,email', 'chambres']);

            return response()->json([
                'success' => true,
                'message' => 'Maison créée avec succès.',
                'data' => $maison
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la maison.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Maison $maison)
    {
        // Vérifier que l'utilisateur peut voir cette maison
        if (!$this->canAccessMaison($maison)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $maison->load(['proprietaire:id,nom,email', 'chambres.locataire:id,nom,email']);
        $maison->setAttribute('image_url', $maison->image_url);

        return response()->json([
            'success' => true,
            'data' => $maison
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaisonRequest $request, Maison $maison)
    {
        try {
            // Vérifier que l'utilisateur peut modifier cette maison
            if (!$this->canAccessMaison($maison)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé.'
                ], 403);
            }

            $data = $request->validated();

            // Gestion de l'upload d'image
            if ($request->hasFile('image')) {
                // Stocker le chemin de l'ancienne image
                $oldImagePath = $maison->image;

                // Stocker la nouvelle image
                $imagePath = $request->file('image')->store('maisons', 'public');
                $data['image'] = $imagePath;

                // Supprimer l'ancienne image après mise à jour réussie
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            $maison->update($data);
            $maison->load(['proprietaire:id,nom,email', 'chambres']);

            return response()->json([
                'success' => true,
                'message' => 'Maison mise à jour avec succès.',
                'data' => $maison
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la maison.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maison $maison)
    {
        try {
            // Vérifier que l'utilisateur peut supprimer cette maison
            if (!$this->canAccessMaison($maison)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé.'
                ], 403);
            }

            // Stocker le chemin de l'image avant suppression
            $imagePath = $maison->image;

            $maison->delete();

            // Supprimer l'image après suppression réussie
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return response()->json([
                'success' => true,
                'message' => 'Maison supprimée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la maison.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    /**
     * Obtenir les maisons du propriétaire connecté
     */
    public function mesMaisons()
    {

        $query = Maison::where('proprietaire_id', Auth::id());

        $maisons = $query->orderBy('id', 'desc')->get();

        $maisons = Maison::with(['chambres'])
            ->withCount('chambres')
            ->byProprietaire(Auth::id())
            ->orderBy('id', 'desc')
            ->get();

        $maisons->each(function ($maison) {
            $maison->setAttribute('nombre_chambres', $maison->chambres_count);
            $maison->setAttribute('image_url', $maison->image_url);
        });

        return response()->json([
            'success' => true,
            'data' => $maisons,
            'total' => $maisons->count()
        ]);
    }

    /**
     * Changer le statut actif/inactif d'une maison
     */
    public function toggleStatus(Maison $maison)
    {
        // Vérifier que l'utilisateur peut modifier cette maison
        if (!$this->canAccessMaison($maison)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $maison->update(['active' => !$maison->active]);

        return response()->json([
            'success' => true,
            'message' => $maison->active ? 'Maison activée.' : 'Maison désactivée.',
            'data' => $maison
        ]);
    }

    public function select()
    {
        $query = Maison::select('id', 'nom');

        // Filtrer selon l'utilisateur connecté s'il n'est pas admin
        if ($this->isProprietaire()) {
            $query->where('proprietaire_id', Auth::id());
        }

        $maisons = $query->orderBy('nom')->get();

        return response()->json([
            'success' => true,
            'data' => $maisons
        ]);
    }

    /**
     * Récupérer les maisons ayant au moins une chambre disponible (pour locataires)
     */
    public function maisonsDisponibles(Request $request)
    {
        $query = Maison::with(['chambres' => function ($q) {
            $q->where('disponible', true);
        }, 'proprietaire:id,nom,email'])
            ->whereHas('chambres', function ($q) {
                $q->where('disponible', true);
            })
            ->where('active', true);

        // Optionnel : ajouter recherche
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('adresse', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        $maisons = $query->orderBy('id', 'desc')->get();

        $maisons->each(function ($maison) {
            $maison->setAttribute('image_url', $maison->image_url);
            $maison->setAttribute('nombre_chambres_disponibles', $maison->chambres->count());
        });

        return response()->json([
            'success' => true,
            'data' => $maisons,
            'total' => $maisons->count()
        ]);
    }


    /**
     * Vérifier si l'utilisateur peut accéder à la maison
     */
    private function canAccessMaison(Maison $maison): bool
    {
        return $this->isAdmin() || $maison->proprietaire_id === Auth::id();
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    private function isAdmin(): bool
    {
        return Auth::user()->role === 'admin';
    }

    private function isProprietaire(): bool
    {
        return Auth::user()->role === 'proprietaire';
    }
}
