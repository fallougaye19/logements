<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreMaisonRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'adresse' => 'required|string',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'proprietaire_id' => 'required|exists:users,id',
        ];
    }
}
