<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Probleme;
use App\Http\Requests\StoreProblemeRequest; // Assuming you have a request class for validation

class ProblemeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Probleme::with('contrat', 'auteur')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProblemeRequest $request)
    {
        $data = $request->validated();
        $data['signale_par'] = auth()->id();
        return Probleme::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Probleme $probleme)
    {
        return $probleme;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Probleme $probleme)
    {
        $probleme->update($request->all());
        $data = $request->validate([
            'contrat_id' => 'sometimes|required|exists:contrats,id',
            'signale_par' => 'sometimes|required|exists:users,id',
            'description' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
            'responsable' => 'sometimes|nullable|string',
            'resolu' => 'sometimes|boolean'
        ]);

        $probleme->update($data);
        return $probleme;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Probleme $probleme)
    {
        $probleme->delete();
        return response()->noContent();
    }
}
