<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\AdminRequestController;

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

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');

        // Gestion des comptes
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::patch('/users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
        Route::patch('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
        Route::patch('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Gestion des jeux
        Route::get('/games', [AdminGameController::class, 'index'])->name('games.index');
        Route::post('/games', [AdminGameController::class, 'store'])->name('games.store');
        Route::delete('/games/{game}', [AdminGameController::class, 'destroy'])->name('games.destroy');

        // Demandes d'ajout de jeux
        Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
        Route::patch('/requests/{gameRequest}/approve', [AdminRequestController::class, 'approve'])->name('requests.approve');
        Route::patch('/requests/{gameRequest}/reject', [AdminRequestController::class, 'reject'])->name('requests.reject');
    });