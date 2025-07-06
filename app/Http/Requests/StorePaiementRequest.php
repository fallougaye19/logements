<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StorePaiementRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'contrat_id' => 'required|exists:contrats,id',
            'montant' => 'required|numeric',
            'statut' => 'nullable|string',
            'date_echeance' => 'required|date',
            'date_paiement' => 'nullable|date',
        ];
    }
}
