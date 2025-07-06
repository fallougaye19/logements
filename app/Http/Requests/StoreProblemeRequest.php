<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreProblemeRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'contrat_id' => 'required|exists:contrats,id',
            'description' => 'required|string',
            'type' => 'nullable|string',
            'responsable' => 'nullable|string',
            'resolu' => 'boolean',
        ];
    }
}
