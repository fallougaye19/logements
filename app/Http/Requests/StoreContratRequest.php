<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreContratRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'locataire_id' => 'required|exists:users,id',
            'chambre_id' => 'required|exists:chambres,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'montant_caution' => 'nullable|numeric',
            'mois_caution' => 'nullable|numeric',
            'description' => 'nullable|string',
            'mode_paiement' => 'nullable|string',
            'periodicite' => 'nullable|string',
            'statut' => 'nullable|string',
        ];
    }
}
