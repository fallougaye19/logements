<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChambreRequest extends FormRequest
{
    public function authorize()
    {
        return true; // à ajuster selon ta logique d'autorisation
    }

    public function rules()
    {
        return [
            'maison_id' => 'required|exists:maisons,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'taille' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'meublee' => 'nullable|boolean',
            'salle_de_bain' => 'nullable|boolean',
            'prix' => 'required|numeric|min:0',
            'disponible' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'maison_id.required' => 'La maison est obligatoire.',
            'maison_id.exists' => 'La maison sélectionnée n\'existe pas.',
            'titre.required' => 'Le titre est obligatoire.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'taille.string' => 'La taille doit être une chaîne de caractères.',
            'taille.max' => 'La taille ne peut pas dépasser 50 caractères.',
            'type.string' => 'Le type doit être une chaîne de caractères.',
            'type.max' => 'Le type ne peut pas dépasser 50 caractères.',
            'meublee.boolean' => 'Le champ meublée doit être vrai ou faux.',
            'salle_de_bain.boolean' => 'La salle de bain doit être une chaîne de caractères.',
            //alle_de_bain.max' => 'La salle de bain ne peut pas dépasser 50 caractères.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0.',
            'disponible.boolean' => 'Le champ disponible doit être vrai ou faux.',
            'images.array' => 'Les images doivent être envoyées sous forme de tableau.',
            'images.*.image' => 'Chaque fichier doit être une image.',
            'images.*.mimes' => 'Les images doivent être au format jpeg, png, jpg, gif ou webp.',
            'images.*.max' => 'Chaque image ne peut pas dépasser 2MB.',
        ];
    }

    protected function prepareForValidation()
    {
        // Convertir les chaînes en booléens
        $this->merge([
            'meublee' => $this->meublee === '1' || $this->meublee === 'true' || $this->meublee === true,
            'salle_de_bain' => $this->salle_de_bain === '1' || $this->salle_de_bain === 'true' || $this->salle_de_bain === true,
            'disponible' => $this->disponible === '1' || $this->disponible === 'true' || $this->disponible === true,
        ]);
    }
}
