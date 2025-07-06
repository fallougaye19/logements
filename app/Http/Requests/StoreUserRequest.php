<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreUserRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string',
            'cni' => 'nullable|string',
            'role' => 'required|in:proprietaire,locataire',
            'password' => 'required|string|min:6',
        ];
    }
}
