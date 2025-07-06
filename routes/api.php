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

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('maisons', MaisonController::class);
    Route::apiResource('chambres', ChambreController::class);
    Route::apiResource('contrats', ContratController::class);
    Route::apiResource('medias', MediaController::class);
    Route::apiResource('paiements', PaiementController::class);
    Route::apiResource('problemes', ProblemeController::class);
    Route::apiResource('rendez-vous', RendezVousController::class);
    Route::apiResource('utilisateurs', UserController::class);
    Route::get('/stats', [StatsController::class, 'stats']);
});

