<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaisonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'adresse' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'sometimes|nullable|numeric|between:-90,90',
            'longitude' => 'sometimes|nullable|numeric|between:-180,180',
            'proprietaire_id' => 'sometimes|required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
            'active' => 'sometimes|boolean'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'adresse.required' => 'L\'adresse est obligatoire.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'adresse.max' => 'L\'adresse ne peut pas dépasser 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'latitude.numeric' => 'La latitude doit être un nombre.',
            'latitude.between' => 'La latitude doit être comprise entre -90 et 90.',

            'longitude.numeric' => 'La longitude doit être un nombre.',
            'longitude.between' => 'La longitude doit être comprise entre -180 et 180.',

            'proprietaire_id.required' => 'Le propriétaire est obligatoire.',
            'proprietaire_id.exists' => 'Le propriétaire sélectionné n\'existe pas.',

            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou webp.',
            'image.max' => 'L\'image ne peut pas dépasser 2MB.',

            'active.boolean' => 'Le statut actif doit être vrai ou faux.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'adresse' => 'adresse',
            'description' => 'description',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'proprietaire_id' => 'propriétaire',
            'image' => 'image',
            'active' => 'statut actif',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérification supplémentaire : si latitude est fournie, longitude doit l'être aussi
            if ($this->has('latitude') && $this->latitude !== null && !$this->has('longitude')) {
                $validator->errors()->add('longitude', 'La longitude est requise lorsque la latitude est spécifiée.');
            }

            // Vérification supplémentaire : si longitude est fournie, latitude doit l'être aussi
            if ($this->has('longitude') && $this->longitude !== null && !$this->has('latitude')) {
                $validator->errors()->add('latitude', 'La latitude est requise lorsque la longitude est spécifiée.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Nettoyer les données avant validation
        if ($this->has('adresse')) {
            $this->merge([
                'adresse' => trim($this->adresse)
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => $this->description ? trim($this->description) : null
            ]);
        }

        // Convertir les coordonnées en nombres si elles sont des chaînes
        if ($this->has('latitude') && is_string($this->latitude)) {
            $this->merge([
                'latitude' => $this->latitude !== '' ? (float) $this->latitude : null
            ]);
        }

        if ($this->has('longitude') && is_string($this->longitude)) {
            $this->merge([
                'longitude' => $this->longitude !== '' ? (float) $this->longitude : null
            ]);
        }

        // Convertir active en booléen si c'est une chaîne
        if ($this->has('active') && is_string($this->active)) {
            $this->merge([
                'active' => filter_var($this->active, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
