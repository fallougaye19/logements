<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rendez_vous;
use App\Http\Requests\StoreRendezVousRequest; // Assuming you have a request class for validation

class RendezVousController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Rendez_vous::with(['locataire','chambre'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRendezVousRequest $request)
    {
        $data = $request->validated();
        return Rendez_vous::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rendez_vous $rendezVous)
    {
        return $rendezVous;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rendez_vous $rendezVous)
    {
        $data = $request->validate([
            'locataire_id' => 'sometimes|required|exists:users,id',
            'chambre_id' => 'sometimes|required|exists:chambres,id',
            'date_heure' => 'sometimes|required|date',
            'statut' => 'sometimes|required|string'
        ]);

        $rendezVous->update($data);
        return $rendezVous;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rendez_vous $rendezVous)
    {
        $rendezVous->delete();
        return response()->noContent();
    }
}
