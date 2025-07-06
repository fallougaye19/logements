<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chambre;
use App\Http\Requests\StoreChambreRequest;

class ChambreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Chambre::with('maison')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChambreRequest $request)
    {
        $data = $request->validated();
        return Chambre::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chambre $chambre)
    {
        return $chambre->load('medias', 'contrats');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chambre $chambre)
    {
        $chambre->update($request->all());
        return $chambre;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chambre $chambre)
    {
        $chambre->delete();
        return response()->noContent();
    }
}
