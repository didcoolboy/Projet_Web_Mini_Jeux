<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_demandeur')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_receveur') ->constrained('users')->onDelete('cascade');
            $table->enum('statut', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
            $table->timestamps();

            // Un seul enregistrement par paire
            $table->unique(['id_demandeur', 'id_receveur']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amis');
    }
};
