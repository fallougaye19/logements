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
            $table->foreignId('chambre_id')->constrained('chambres')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('montant_caution', 10, 2)->nullable();
            $table->unsignedTinyInteger('mois_caution')->nullable();
            $table->text('description')->nullable();
            $table->string('mode_paiement')->nullable();
            $table->string('periodicite')->nullable();
            $table->string('statut')->default('actif');
            $table->timestamp('cree_le')->nullable();
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
