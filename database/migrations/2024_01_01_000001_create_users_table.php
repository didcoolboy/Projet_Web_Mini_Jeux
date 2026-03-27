<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('pseudo', 50)->unique();
            $table->string('mot_de_passe');
            $table->unsignedBigInteger('score_total')->default(0);
            $table->unsignedInteger('nb_parties')->default(0);
            $table->timestamp('derniere_activite')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
