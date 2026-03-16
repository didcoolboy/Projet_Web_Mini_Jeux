<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // ex: snake, tetris, pong, asteroids
            $table->string('name');           // ex: Snake, Tetris
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        // Seed des jeux par défaut
        DB::table('games')->insert([
            ['slug' => 'snake',    'name' => 'Snake',    'icon' => '🐍', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'tetris',   'name' => 'Tetris',   'icon' => '🧱', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'pong',     'name' => 'Pong',     'icon' => '🏓', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'asteroids','name' => 'Asteroids','icon' => '☄️', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
