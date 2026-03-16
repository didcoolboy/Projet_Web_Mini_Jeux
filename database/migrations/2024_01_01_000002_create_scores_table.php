<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Si tu as déjà cette table, tu peux ignorer cette migration.
     * Elle est fournie pour référence / complétion.
     *
     * Structure attendue : scores(user_id, game_id, score, created_at)
     */
    public function up(): void
    {
        // Ne crée la table que si elle n'existe pas encore
        if (!Schema::hasTable('scores')) {
            Schema::create('scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('game_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('score')->default(0);
                $table->timestamps();

                // Index pour accélérer les classements
                $table->index(['game_id', 'score']);
                $table->index(['user_id', 'game_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
