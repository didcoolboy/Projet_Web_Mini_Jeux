<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Score;
use App\Models\Game;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicGameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\AmiController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminGameController;
use App\Http\Controllers\Admin\AdminRequestController;

// ── Pages publiques (existantes — ne pas toucher) ──────────────────────────

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/accueil', function () {
    $topScores = Score::with(['user', 'game'])
        ->whereRaw('scores.id = (SELECT s2.id FROM scores s2 WHERE s2.user_id = scores.user_id AND s2.game_id = scores.game_id ORDER BY s2.score DESC, s2.id DESC LIMIT 1)')
        ->orderByDesc('scores.score')
        ->take(10)
        ->get();

    $uploadedGames = Game::query()
        ->whereNotIn('slug', ['snake', 'morpion', 'tetris', 'pong', 'memory', 'flappy'])
        ->latest()
        ->get();

    return view('accueil', compact('topScores', 'uploadedGames'));
})->name('accueil');

Route::get('/connexion', [AuthController::class, 'showConnexion'])->name('connexion');
Route::post('/connexion', [AuthController::class, 'connexion'])->name('connexion.post');

Route::get('/inscription', [AuthController::class, 'showInscription'])->name('inscription');
Route::post('/inscription', [AuthController::class, 'inscription'])->name('inscription.post');

Route::get('/invite', [AuthController::class, 'showInvite'])->name('accueil.invite');

Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgotPassword'])->name('mot_de_passe_oublie');
Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::get('/reset-mot-de-passe/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-mot-de-passe', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/deconnexion', [AuthController::class, 'deconnexion'])->name('deconnexion');

// ── Jeux publics (existants — ne pas toucher) ──────────────────────────────

Route::get('/jeux/snake',   function () { return view('jeux.snake');   })->name('jeux.snake');
Route::get('/jeux/morpion', function () { return view('jeux.morpion'); })->name('jeux.morpion');
Route::get('/jeux/tetris',  function () { return view('jeux.tetris');  })->name('jeux.tetris');
Route::get('/jeux/pong',    function () { return view('jeux.pong');    })->name('jeux.pong');
Route::get('/jeux/memory',  function () { return view('jeux.memory');  })->name('jeux.memory');
Route::get('/jeux/flappy',  function () { return view('jeux.flappy');  })->name('jeux.flappy');
Route::get('/jeux/{slug}', [PublicGameController::class, 'show'])->name('jeux.dynamic');

// ── Pages protégées ────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Classement (existant)
    Route::get('/classement', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Profil
    Route::get('/profil/{user}',            [ProfilController::class, 'show'])      ->name('profil');
    Route::get('/profil/{user}/historique', [ProfilController::class, 'historique'])->name('profil.historique');
    Route::get('/profil/edit',              [ProfilController::class, 'edit'])      ->name('profil.edit');
    Route::patch('/profil/settings',        [ProfilController::class, 'settings']) ->name('profil.settings');
    Route::delete('/profil',                [ProfilController::class, 'destroy'])  ->name('profil.destroy');

    // Amis
    Route::get   ('/amis',                  [AmiController::class, 'index'])   ->name('amis.index');
    Route::post  ('/amis/envoyer',          [AmiController::class, 'envoyer'])->name('amis.envoyer');
    Route::patch ('/amis/{friendship}/accepter', [AmiController::class, 'accepter'])->name('amis.accepter');
    Route::delete('/amis/{friendship}/refuser',  [AmiController::class, 'refuser']) ->name('amis.refuser');
    Route::delete('/amis/{user}/retirer',        [AmiController::class, 'retirer']) ->name('amis.retirer');

    // Enregistrement des scores
    Route::post('/save-score/{gameSlug}', [GameController::class, 'saveScore'])->name('save-score');

});

// ── Admin (existant — ne pas toucher) ─────────────────────────────────────

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::get('/', [AdminUserController::class, 'dashboard'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/ban',     [AdminUserController::class, 'ban'])    ->name('users.ban');
        Route::patch('/users/{user}/unban',   [AdminUserController::class, 'unban'])  ->name('users.unban');
        Route::patch('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
        Route::patch('/users/{user}/demote',  [AdminUserController::class, 'demote']) ->name('users.demote');
        Route::delete('/users/{user}',        [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/games',         [AdminGameController::class, 'index'])  ->name('games.index');
        Route::post('/games',        [AdminGameController::class, 'store'])  ->name('games.store');
        Route::delete('/games/{game}', [AdminGameController::class, 'destroy'])->name('games.destroy');

        Route::get('/requests',                               [AdminRequestController::class, 'index'])  ->name('requests.index');
        Route::patch('/requests/{gameRequest}/approve',       [AdminRequestController::class, 'approve'])->name('requests.approve');
        Route::patch('/requests/{gameRequest}/reject',        [AdminRequestController::class, 'reject']) ->name('requests.reject');
    });
