<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Probleme extends Model
{
    use HasFactory;
    protected $fillable = [
        'contrat_id',
        'signale_par',
        'description',
        'type',
        'responsable',
        'resolu'
    ];
    public $timestamps = false;

    public function contrat() {
        return $this->belongsTo(Contrat::class);
    }
    public function auteur()   {
        return $this->belongsTo(User::class, 'signale_par');
    }
}
