<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreRendezVousRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'locataire_id' => 'required|exists:users,id',
            'chambre_id' => 'required|exists:chambres,id',
            'date_heure' => 'required|date',
            'statut' => 'nullable|string',
        ];
    }
}
