<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paiement;
use App\Http\Requests\StorePaiementRequest;
use Illuminate\Support\Facades\Auth;
 // Assuming you have a request class for validation

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Paiement::with('contrat.chambre');

        if ($user->role === 'proprietaire') {
            $query->whereHas('contrat', fn($q) => $q->where('proprietaire_id', $user->id));
        } elseif ($user->role === 'locataire') {
            $query->whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id));
        } elseif ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $paiements = $query->orderBy('date_echeance', 'desc')->get();
        return response()->json(['success' => true, 'data' => $paiements]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaiementRequest $request)
    {
        $data = $request->validated();
        $data['statut'] = 'impayé';
        $paiement = Paiement::create($data);

        return response()->json(['success' => true, 'data' => $paiement], 201);
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
            'statut' => 'sometimes|required|in: impayé,payé,en_retard',
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

    public function retard()
    {
        $user = Auth::user();
        $query = Paiement::with('contrat.chambre')
            ->where('date_echeance', '<', now())
            ->where('statut', 'impayé');

        if ($user->role === 'proprietaire') {
            $query->whereHas('contrat', fn($q) => $q->where('proprietaire_id', $user->id));
        } elseif ($user->role === 'locataire') {
            $query->whereHas('contrat', fn($q) => $q->where('locataire_id', $user->id));
        } elseif ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $paiements = $query->get();
        return response()->json(['success' => true, 'data' => $paiements]);
    }

    public function marquerPaye(Paiement $paiement)
    {
        $user = Auth::user();

        if ($user->role === 'proprietaire') {
            $proprietaire_id = $paiement->contrat->proprietaire_id;
            if ($proprietaire_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
            }
        } elseif ($user->role === 'locataire') {
            $locataire_id = $paiement->contrat->locataire_id;
            if ($locataire_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
            }
            } else {
            return response()->json(['success' => false, 'message' => 'Accès refusé.'], 403);
        }

        $paiement->update([
            'statut' => 'payé',
            'date_paiement' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paiement marqué comme effectué.',
            'data' => $paiement->load('contrat.chambre.maison')
        ]);
    }
}
