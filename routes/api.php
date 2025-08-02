<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MaisonController;
use App\Http\Controllers\Api\ChambreController;
use App\Http\Controllers\Api\ContratController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\ProblemeController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\StatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    //maisons
    Route::apiResource('/maisons', MaisonController::class);
    Route::patch('/maisons/{maison}/toggle-status', [MaisonController::class, 'toggleStatus']);
    Route::get('/maisons/select', [MaisonController::class, 'select']);
    Route::get('/maisons/mes', [MaisonController::class, 'mesMaisons']);
    Route::get('/maisons/disponibles', [MaisonController::class, 'maisonsDisponibles']);
    //chambres
    Route::get('/chambres/search', [ChambreController::class, 'search']);
    Route::get('chambres/available', [ChambreController::class, 'available']);
    Route::get('/chambres/mes', [ChambreController::class, 'mesChambres']);
    Route::get('/chambres/disponibles', [ChambreController::class, 'chambresDisponibles']);
    Route::get('/chambres/libres', [ChambreController::class, 'chambresLibres']);
    Route::post('/chambres/{chambre}/reserve', [ChambreController::class, 'reserve']);
    Route::post('/chambres/{chambre}/accepter', [ChambreController::class, 'accepterReservation']);
    Route::post('/chambres/{chambre}/refuser', [ChambreController::class, 'refuserReservation']);
    Route::apiResource('/chambres', ChambreController::class);
    //contrats
    Route::apiResource('contrats', ContratController::class);
    Route::post('/contrats/{contrat}/resilier', [ContratController::class, 'resilier']);
    //medias
    Route::apiResource('/medias', MediaController::class);
    // paiements
    Route::apiResource('/paiements', PaiementController::class);
    Route::post('/paiements/{paiement}/marquer-payé', [PaiementController::class, 'marquerPaye']);
    //problèmes
    Route::apiResource('/problemes', ProblemeController::class);
    Route::post('/problemes/{probleme}/resoudre', [ProblemeController::class, 'resoudre']);
    //rendez-vous
    Route::apiResource('/rendez-vous', RendezVousController::class);
    Route::patch('/rendez-vous/{rendez_vous}/statut', [RendezVousController::class, 'updateStatut']);
    //utilisateurs
    Route::apiResource('/utilisateurs', UserController::class);
    //reservations
    Route::get('/reservations', [ReservationController::class, 'index']);
    //Route::get('/stats', [StatsController::class, 'stats']);
});
