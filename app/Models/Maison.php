<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Chambre;

class Maison extends Model
{
    use HasFactory;
    protected $fillable = [
        'adresse',
        'description',
        'proprietaire_id',
        'latitude',
        'longitude'
    ];
    public $timestamps = false;

    public function proprietaire() {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }
    public function chambres()     {
        return $this->hasMany(Chambre::class);
    }
}
