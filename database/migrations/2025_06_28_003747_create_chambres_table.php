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
        Schema::create('chambres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maison_id')->constrained('maisons')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('taille')->nullable();
            $table->string('type')->nullable();
            $table->boolean('meublee')->default(false);
            $table->boolean('salle_de_bain')->default(false);
            $table->decimal('prix', 10, 2)->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamp('cree_le')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chambres');
    }
};
