<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Chambre;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Maison extends Model
{
    use HasFactory;
    protected $fillable = [
        'adresse',
        'description',
        'proprietaire_id',
        'latitude',
        'longitude',
        'image',
        'active'
    ];

    public $timestamps = true;

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function proprietaire() {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }
    public function chambres()     {
        return $this->hasMany(Chambre::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => 
            isset($attributes['image']) && $attributes['image']
                ? asset('storage/' . $attributes['image'])
                : null,
        );
    }

    // Scope pour filtrer les maisons d'un propriétaire
    public function scopeByProprietaire($query, $proprietaireId)
    {
        return $query->where('proprietaire_id', $proprietaireId);
    }

    // Scope pour les maisons actives
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Méthode pour obtenir le nombre de chambres
    public function getNombreChambresAttribute()
    {
        if (isset($this->attributes['chambres_count'])) {
            return $this->attributes['chambres_count'];
        }
        return $this->chambres()->count();
    }
}
