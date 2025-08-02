<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'proprietaire') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé.'
            ], 403);
        }

        $reservations = Reservation::with(['chambre.maison', 'chambre.medias', 'locataire'])
            ->whereHas('chambre.maison', fn($q) => $q->where('proprietaire_id', $user->id))
            ->where('statut', 'en_attente')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }
}
