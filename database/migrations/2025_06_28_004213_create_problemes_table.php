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
        Schema::create('problemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('signale_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description');
            $table->enum('type', ['plomberie', 'electricite', 'serrurerie', 'autre'])->default('autre');
            $table->enum('responsable', ['locataire', 'proprietaire', 'indetermine'])->nullable();
            $table->boolean('resolu')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problemes');
    }
};
