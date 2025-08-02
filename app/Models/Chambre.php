<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maison;
use App\Models\Contrat;
use App\Models\Rendez_vous;
use App\Models\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Chambre extends Model
{
    use HasFactory;

    protected $fillable = [
        'maison_id',
        'titre',
        'description',
        'taille',
        'type',
        'meublee',
        'salle_de_bain',
        'prix',
        'disponible',
        'image'
    ];

    public $timestamps = true;

    protected $casts = [
        'meublee' => 'boolean',
        'disponible' => 'boolean',
        'prix' => 'decimal:2',
    ];

    protected $appends = ['image_url'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Chargement eager pour éviter le N+1
                if (!$this->relationLoaded('medias')) {
                    $this->load('medias');
                }

                $media = $this->medias->first();
                return $media ? ($media->url ?? asset('storage/' . $media->path)) : null;
            }
        );
    }

    // Ajouter un accesseur pour l'URL de l'image principale
    public function getImageUrlAttribute()
    {
        // Charger la relation si elle n'est pas déjà chargée
        if (!$this->relationLoaded('medias')) {
            $this->load('medias');
        }

        if ($this->medias->isNotEmpty()) {
            return $this->medias->first()->url ?? asset('storage/' . $this->medias->first()->path);
        }

        return null;
    }

    // Dans la méthode boot() pour toujours charger les médias
    protected static function booted()
    {
        static::addGlobalScope('withMedias', function ($builder) {
            $builder->with('medias');
        });
    }

    public function maison()
    {
        return $this->belongsTo(Maison::class);
    }

    public function medias()
    {
        return $this->hasMany(Media::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function rendezVous()
    {
        return $this->hasMany(Rendez_vous::class);
    }
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }
}
