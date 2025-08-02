<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChambreRequest extends FormRequest
{
    public function authorize()
    {
        // Ajuste selon ta logique d'autorisation
        return true;
    }

    public function rules()
    {
        return [
            'maison_id' => 'sometimes|required|exists:maisons,id',
            'titre' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'taille' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'meublee' => 'nullable|boolean',
            'salle_de_bain' => 'nullable|boolean',
            'prix' => 'sometimes|required|numeric|min:0',
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

            'salle_de_bain.boolean' => 'La salle de bain doit être un boolean',
            //alle_de_bain.max' => 'La salle de bain ne peut pas dépasser 50 caractères.',

            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix doit être supérieur ou égal à 0.',

            'disponible.boolean' => 'Le champ disponible doit être vrai ou faux.',

            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou webp.',
            'image.max' => 'L\'image ne peut pas dépasser 2MB.',
        ];
    }

    public function attributes()
    {
        return [
            'maison_id' => 'maison',
            'titre' => 'titre',
            'description' => 'description',
            'taille' => 'taille',
            'type' => 'type',
            'meublee' => 'meublée',
            'salle_de_bain' => 'salle de bain',
            'prix' => 'prix',
            'disponible' => 'disponibilité',
            'image' => 'image',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('titre')) {
            $this->merge(['titre' => trim($this->titre)]);
        }

        if ($this->has('description')) {
            $this->merge(['description' => $this->description ? trim($this->description) : null]);
        }

        if ($this->has('taille')) {
            $this->merge(['taille' => $this->taille ? trim($this->taille) : null]);
        }

        if ($this->has('type')) {
            $this->merge(['type' => $this->type ? trim($this->type) : null]);
        }

        if ($this->has('salle_de_bain')) {
            $this->merge(['salle_de_bain' => $this->salle_de_bain ? trim($this->salle_de_bain) : null]);
        }

        // Convertir booleans issus de chaînes en bool
        if ($this->has('meublee') && is_string($this->meublee)) {
            $this->merge(['meublee' => filter_var($this->meublee, FILTER_VALIDATE_BOOLEAN)]);
        }
        if ($this->has('disponible') && is_string($this->disponible)) {
            $this->merge(['disponible' => filter_var($this->disponible, FILTER_VALIDATE_BOOLEAN)]);
        }
    }
}
