<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;
    protected $fillable = [
        'locataire_id',
        'chambre_id',
        'date_debut',
        'date_fin',
        'montant_caution',
        'mois_caution',
        'description',
        'mode_paiement',
        'periodicite',
        'statut'
    ];
    public $timestamps = false;

    public function locataire() {
        return $this->belongsTo(User::class, 'locataire_id');
    }
    public function chambre()   {
        return $this->belongsTo(Chambre::class);
    }
    public function paiements() {
        return $this->hasMany(Paiement::class);
    }
    public function problemes() {
        return $this->hasMany(Probleme::class);
    }
}
