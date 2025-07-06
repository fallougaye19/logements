<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maison;
use App\Http\Requests\StoreMaisonRequest;

class MaisonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Maison::with('proprietaire')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMaisonRequest $request)
    {

        $data = $request->validated();
        return Maison::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Maison $maison)
    {
        return $maison->load('chambres');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maison $maison)
    {
        $data = $request->validate([
        'adresse' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'proprietaire_id' => 'sometimes|required|exists:users,id',
        'latitude' => 'sometimes|required|numeric',
        'longitude' => 'sometimes|required|numeric'
        ]);

        $maison->update($data);
        return $maison;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maison $maison)
    {
        $maison->delete();
        return response()->noContent();
    }
}
