<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['chambre_id', 'locataire_id', 'statut', 'message'];

    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }
    public function locataire()
    {
        return $this->belongsTo(User::class, 'locataire_id');
    }
}
