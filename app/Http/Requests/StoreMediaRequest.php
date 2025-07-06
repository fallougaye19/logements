<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreMediaRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'chambre_id' => 'required|exists:chambres,id',
            'url' => 'required|string',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }
}
