<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $fillable = [
        'contrat_id',
        'montant',
        'statut',
        'date_echeance',
        'date_paiement'
    ];
    public $timestamps = false;

    public function contrat() {
        return $this->belongsTo(Contrat::class);
    }
}
