<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maison;
use App\Models\Contrat;
use App\Models\Rendez_vous;
use App\Models\Media;

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
        'disponible'
    ];
    public $timestamps = false;

    public function maison(){
        return $this->belongsTo(Maison::class);
    }
    public function medias() {
        return $this->hasMany(Media::class);
    }
    public function contrats() {
        return $this->hasMany(Contrat::class);
    }
    public function rendezVous() {
        return $this->hasMany(Rendez_vous::class);
    }
}
