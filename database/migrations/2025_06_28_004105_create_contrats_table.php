<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locataire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chambre_id')->constrained('chambres')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('montant_caution', 10, 2)->nullable();
            $table->tinyInteger('mois_caution')->unsigned()->default(1);
            $table->text('description')->nullable();
            $table->enum('mode_paiement',['virement', 'espèces', 'mobile_money', 'chèque'])->nullable();
            $table->enum('periodicite',['journalier', 'hebdomadaire', 'mensuel'])->default('mensuel');
            $table->enum('statut', ['actif', 'resilie', 'expiré'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
