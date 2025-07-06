<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreChambreRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'maison_id' => 'required|exists:maisons,id',
            'titre' => 'required|string',
            'description' => 'nullable|string',
            'taille' => 'nullable|string',
            'type' => 'nullable|string',
            'meublee' => 'boolean',
            'salle_de_bain' => 'boolean',
            'prix' => 'nullable|numeric',
            'disponible' => 'boolean',
        ];
    }
}
