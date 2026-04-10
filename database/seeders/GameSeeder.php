<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use Illuminate\Support\Facades\File;

class GameSeeder extends Seeder
{
    public function run()
    {
        $games = [
            [
                'slug' => 'snake',
                'name' => 'Snake',
                'icon' => '🐍',
                'description' => 'Mange, grandis, survive. Le classique indémodable.',
                'js_file' => 'snake.js',
            ],
            [
                'slug' => 'tetris',
                'name' => 'Tetris',
                'icon' => '🧱',
                'description' => 'Arrange les blocs pour compléter les lignes.',
                'js_file' => 'tetris.js',
            ],
            [
                'slug' => 'pong',
                'name' => 'Pong',
                'icon' => '🏓',
                'description' => 'Le jeu de tennis virtuel classique.',
                'js_file' => 'pong.js',
            ],
            [
                'slug' => 'memory',
                'name' => 'Memory',
                'icon' => '🧠',
                'description' => 'Teste ta mémoire en trouvant les paires.',
                'js_file' => 'memory.js',
            ],
            [
                'slug' => 'morpion',
                'name' => 'Morpion',
                'icon' => '⭕',
                'description' => 'Le jeu de tic-tac-toe classique.',
                'js_file' => 'morpion.js',
            ],
            [
                'slug' => 'flappy',
                'name' => 'Flappy Bird',
                'icon' => '🐦',
                'description' => 'Fais voler l\'oiseau sans toucher les tuyaux.',
                'js_file' => 'flappy.js',
            ],
        ];

        foreach ($games as $gameData) {
            $code = null;
            if ($gameData['js_file']) {
                $path = public_path('js/jeux/' . $gameData['js_file']);
                if (File::exists($path)) {
                    $code = File::get($path);
                }
            }
            unset($gameData['js_file']);
            $gameData['code'] = $code;

            Game::updateOrCreate(
                ['slug' => $gameData['slug']],
                $gameData
            );
        }
    }
}
