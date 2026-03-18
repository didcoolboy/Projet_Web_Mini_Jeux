<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaderboardController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/accueil', function () {
    return view('accueil');
})->name('accueil');

Route::get('/connexion', [AuthController::class, 'showConnexion'])->name('connexion');
Route::post('/connexion', [AuthController::class, 'connexion'])->name('connexion.post');

Route::get('/inscription', [AuthController::class, 'showInscription'])->name('inscription');
Route::post('/inscription', [AuthController::class, 'inscription'])->name('inscription.post');

Route::get('/invite', [AuthController::class, 'showInvite'])->name('accueil.invite');

Route::get('/mot-de-passe-oublie', function () {
    return view('auth.mot_de_passe_oublie');
})->name('mot_de_passe_oublie');

Route::post('/deconnexion', [AuthController::class, 'deconnexion'])->name('deconnexion');

Route::middleware('auth')->group(function () {
    Route::get('/classement', [LeaderboardController::class, 'index'])
         ->name('leaderboard.index');
});

// route des jeux déja fait
Route::get('/jeux/snake',   function () { return view('jeux.snake');   })->name('jeux.snake');
Route::get('/jeux/morpion', function () { return view('jeux.morpion'); })->name('jeux.morpion');
Route::get('/jeux/tetris',  function () { return view('jeux.tetris');  })->name('jeux.tetris');
Route::get('/jeux/pong',    function () { return view('jeux.pong');    })->name('jeux.pong');
Route::get('/jeux/memory',  function () { return view('jeux.memory');  })->name('jeux.memory');
Route::get('/jeux/flappy',  function () { return view('jeux.flappy');  })->name('jeux.flappy');