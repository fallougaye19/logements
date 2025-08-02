<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Chambre;
use App\Http\Requests\StoreChambreRequest;
use App\Http\Requests\UpdateChambreRequest;
use App\Models\Maison;
use App\Models\Contrat;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ChambreController extends Controller
{
    public function index(Request $request)
    {
        $query = Chambre::with(['maison', 'medias']);

        // Filtres
        if ($request->has('disponible')) {
            $query->where('disponible', $request->boolean('disponible'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Restriction par rôle
        if ($this->isProprietaire()) {
            $query->whereHas('maison', function ($q) {
                $q->where('proprietaire_id', Auth::id());
            });
        } elseif ($this->isLocataire()) {
            // $query->whereHas('contrats', function ($q) {
            //     $q->where('locataire_id', Auth::id());
            // });
        } elseif (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $chambres = $query->orderBy('id', 'desc')->get();
        $chambres->each(fn($chambre) => $chambre->setAttribute('image_url', $chambre->image_url));

        return response()->json(['success' => true, 'data' => $chambres]);
    }

    public function store(StoreChambreRequest $request)
    {
        $data = $request->validated();

        $maison = Maison::find($data['maison_id']);

        if (!$maison || $maison->proprietaire_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Maison non trouvée ou accès non autorisé'
            ], 403);
        }



        // Création de la chambre
        $chambre = Chambre::create($data);

        // Ajout des images si présentes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('chambres', 'public');
                $url = asset('storage/' . $path);
                $chambre->medias()->create([
                    'path' => $path,
                    'url' => $url
                ]);
            }
        }

        $chambre->load(['maison', 'contrats', 'medias']);
        $chambre->setAttribute('image_url', $chambre->image_url);

        return response()->json(['success' => true, 'message' => 'Chambre créée.', 'data' => $chambre], 201);
    }


    public function show(Chambre $chambre)
    {
        if (!$this->canAccessChambre($chambre)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $chambre->load(['maison', 'contrats', 'medias']);
        $chambre->setAttribute('image_url', $chambre->image_url);

        return response()->json(['success' => true, 'data' => $chambre]);
    }

    public function update(UpdateChambreRequest $request, Chambre $chambre)
    {
        if (!$this->canAccessChambre($chambre)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $data = $request->validated();

        $chambre->update($data);

        // Gestion des nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('chambres', 'public');
                $url = asset('storage/' . $path);
                $chambre->medias()->create([
                    'path' => $path,
                    'url' => $url
                ]);
            }
        }

        // Si maison modifiée, vérifier accès
        if (isset($data['maison_id']) && !$this->canAccessMaisonId($data['maison_id'])) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé à la maison.'], 403);
        }

        // Mise à jour de la chambre (sans image)
        unset($data['images']); // On gère les images après
        $chambre->update($data);

        if ($request->hasFile('image')) {
            $oldImage = $chambre->image;
            $data['image'] = $request->file('image')->store('chambres', 'public');

            // Supprime ancienne image
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $chambre->update($data);
        $chambre->load(['maison', 'contrats', 'medias']);
        $chambre->setAttribute('image_url', $chambre->image_url);

        return response()->json(['success' => true, 'message' => 'Chambre mise à jour.', 'data' => $chambre]);
    }

    public function destroy(Chambre $chambre)
    {
        if (!$this->canAccessChambre($chambre)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $oldImage = $chambre->image;
        $chambre->delete();

        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return response()->json(['success' => true, 'message' => 'Chambre supprimée.']);
    }

    public function toggleDisponibilite(Chambre $chambre)
    {
        if (!$this->canAccessChambre($chambre)) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $chambre->update(['disponible' => !$chambre->disponible]);

        return response()->json([
            'success' => true,
            'message' => $chambre->disponible ? 'Chambre disponible.' : 'Chambre non disponible.',
            'data' => $chambre
        ]);
    }

    private function isAdmin(): bool
    {
        return Auth::user()->role === 'admin';
    }

    private function isProprietaire(): bool
    {
        return Auth::user()->role === 'proprietaire';
    }

    private function isLocataire(): bool
    {
        return Auth::user()->role === 'locataire';
    }

    private function canAccessChambre(Chambre $chambre): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $userId = Auth::id();

        if ($this->isProprietaire()) {
            return $chambre->maison->proprietaire_id === $userId;
        }

        if ($this->isLocataire()) {
            return $chambre->contrats()->where('locataire_id', $userId)->exists();
        }

        return false;
    }

    private function canAccessMaisonId(int $maisonId): bool
    {
        if ($this->isAdmin()) return true;

        return Maison::where('id', $maisonId)
            ->where('proprietaire_id', Auth::id())
            ->exists();
    }
    public function search(Request $request)
    {
        $query = Chambre::with(['maison', 'medias']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('titre', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
        }

        if ($request->filled('disponible')) {
            $query->where('disponible', (bool)$request->disponible);
        }

        if ($this->isProprietaire()) {
            $query->whereHas('maison', fn($q) => $q->where('proprietaire_id', Auth::id()));
        } elseif (!$this->isAdmin() && !$this->isLocataire()) {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $chambres = $query->get();
        $chambres->each(fn($c) => $c->setAttribute('image_url', $c->image_url));

        return response()->json(['success' => true, 'data' => $chambres]);
    }

    public function mesChambres()
    {
        if (!$this->isProprietaire()) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $query = Chambre::with(['maison', 'medias'])
            ->whereHas('maison', function ($q) {
                $q->where('proprietaire_id', Auth::id());
            });

        $chambres = $query->orderBy('id', 'desc')->get();

        $chambres->each(function ($chambre) {
            $chambre->setAttribute('image_url', $chambre->image_url);
        });

        return response()->json([
            'success' => true,
            'data' => $chambres,
            'total' => $chambres->count()
        ]);
    }

    public function chambresDisponibles(Request $request)
{
    try {
        $request->validate([
            'search' => 'sometimes|string|max:255',
            'max_price' => 'sometimes|numeric|min:0',
            'min_size' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|string|max:50',
        ]);

        $query = Chambre::with(['maison', 'medias'])
            ->where('disponible', true)
            ->whereHas('maison', function ($q) {
                $q->where('active', true);
            });

        // Filtres
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('type', 'LIKE', "%{$search}%")
                  ->orWhereHas('maison', function ($q) use ($search) {
                      $q->where('adresse', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('max_price')) {
            $query->where('prix', '<=', $request->input('max_price'));
        }

        if ($request->filled('min_size')) {
            $query->where('taille', '>=', $request->input('min_size'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $chambres = $query->orderBy('prix', 'asc')->get();

        // 🔹 Ajouter image_url via map() sans modifier le modèle
        $chambres = $chambres->map(function ($chambre) {
            $imageUrl = $chambre->medias && $chambre->medias->isNotEmpty()
                ? $chambre->medias->first()->url
                : asset('images/default.jpg'); // ✅ `asset()` pour URL complète

            return [
                'id' => $chambre->id,
                'titre' => $chambre->titre,
                'description' => $chambre->description,
                'prix' => $chambre->prix,
                'taille' => $chambre->taille,
                'type' => $chambre->type,
                'disponible' => $chambre->disponible,
                'image_url' => $imageUrl,
                'maison' => [
                    'id' => $chambre->maison->id,
                    'adresse' => $chambre->maison->adresse,
                ],
                'medias' => $chambre->medias,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $chambres
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur serveur',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Dans ChambreController.php

    public function reserve(Request $request, Chambre $chambre)
    {
        $user = Auth::user();

        if ($user->role !== 'locataire') {
            return response()->json([
                'success' => false,
                'message' => 'Seul un locataire peut réserver.'
            ], 403);
        }

        // Vérifier si déjà réservée
        $existing = Reservation::where('chambre_id', $chambre->id)
            ->where('locataire_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->statut === 'en_attente') {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre réservation est déjà en attente.'
                ]);
            } elseif ($existing->statut === 'acceptee') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà réservé cette chambre.'
                ]);
            }
        }

        // Créer ou mettre à jour la réservation
        Reservation::updateOrCreate(
            [
                'chambre_id' => $chambre->id,
                'locataire_id' => $user->id,
            ],
            [
                'statut' => 'en_attente',
                'message' => $request->input('message') // Optionnel
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Réservation envoyée au propriétaire.'
        ]);
    }

    // ChambreController.php

    public function accepterReservation(Chambre $chambre)
    {
        $chambre->load('reservation.locataire', 'maison');
        $reservation = $chambre->reservation;
        if (!$reservation || $reservation->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Aucune réservation en attente.'
            ], 400);
        }

        if ($reservation->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'La réservation n’est plus en attente.'
            ], 400);
        }

        // Vérifier que le propriétaire gère bien cette chambre
        if ($chambre->maison->proprietaire_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        DB::beginTransaction();
        try {
            // 1. Marquer la réservation comme acceptée
            $reservation->update(['statut' => 'acceptee']);
            // 2. Générer un contrat
            $contrat = new Contrat();
            $contrat->chambre_id = $chambre->id;
            $contrat->locataire_id = $reservation->locataire_id;
            $contrat->proprietaire_id = Auth::id();
            $contrat->date_debut = now();
            $contrat->date_fin = null;
            $contrat->montant_caution = ($chambre->prix ?? 0) * 2;
            $contrat->mois_caution = 2;
            $contrat->mode_paiement = 'virement';
            $contrat->periodicite = 'mensuel';
            $contrat->statut = 'actif';
            $contrat->save();

            // 3. Marquer la chambre comme non disponible
            $chambre->update(['disponible' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation acceptée et contrat généré.',
                'contrat' => $contrat
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'acceptation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function refuserReservation(Chambre $chambre)
    {
        $reservation = $chambre->reservation;
        if (!$reservation || $reservation->statut !== 'en_attente') {
            return response()->json([
                'success' => false,
                'message' => 'Aucune réservation en attente.'
            ], 400);
        }

        if ($chambre->maison->proprietaire_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Accès non autorisé.'], 403);
        }

        $reservation->update(['statut' => 'refusee']);

        return response()->json([
            'success' => true,
            'message' => 'Réservation refusée.'
        ]);
    }
}
