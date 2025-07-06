<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Maison;
use App\Models\Contrat;
use App\Models\Probleme;
use App\Models\Rendez_vous;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'cni',
        'role',
        'password'
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function maisons(){
        return $this->hasMany(Maison::class, 'proprietaire_id');
    }
    public function contrats(){
        return $this->hasMany(Contrat::class, 'locataire_id');
    }
    public function problemes(){
        return $this->hasMany(Probleme::class, 'signale_par');
    }
    public function rendezVous(){
        return $this->hasMany(Rendez_Vous::class, 'locataire_id');
    }
}
