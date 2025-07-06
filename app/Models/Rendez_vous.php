<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendez_vous extends Model
{
    use HasFactory;
    protected $fillable = [
        'locataire_id',
        'chambre_id',
        'date_heure',
        'statut'
    ];
    public $timestamps = false;

    public function locataire() {
        return $this->belongsTo(User::class, 'locataire_id');
    }
    public function chambre()   {
        return $this->belongsTo(Chambre::class);
    }
}
