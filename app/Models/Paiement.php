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
    public $timestamps = true;

    protected $casts = [
        'cree_le' => 'datetime',
        'date_echeance' => 'datetime',
        'date_paiement' => 'datetime',
        'montant' => 'decimal:2',
    ];

    public static $rules = [
        'type' => ['in:plomberie,electricite,serrurerie,autre'],
        'responsable' => ['in:locataire,proprietaire,indetermine'],
    ];

    public function contrat() {
        return $this->belongsTo(Contrat::class);
    }
}
