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
            $table->string('type')->default('autre');
            $table->string('responsable')->nullable();
            $table->boolean('resolu')->default(false);
            $table->timestamp('cree_le')->nullable();
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
