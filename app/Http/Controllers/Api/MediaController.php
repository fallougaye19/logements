<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use App\Http\Requests\StoreMediaRequest; // Assuming you have a request class for validation

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Media::with('chambre')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMediaRequest $request)
    {
        $data = $request->validated();
        return Media::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return $media;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Media $media)
    {
        $data = $request->validate([
            'chambre_id' => 'sometimes|required|exists:chambres,id',
            'url' => 'sometimes|required|url',
            'type' => 'sometimes|required|string',
            'description' => 'nullable|string'
        ]);

        $media->update($data);
        return $media;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        $media->delete();
        return response()->noContent();
    }
}
