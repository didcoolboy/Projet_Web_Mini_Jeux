<?php

use App\Http\Controllers\AmiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes PixelZone
|--------------------------------------------------------------------------
*/

// Pages publiques
Route::get('/',        fn() => view('welcome'))->name('home');
Route::get('/jeux',    fn() => view('jeux'))->name('jeux');
Route::get('/profil/{user}', fn($user) => view('profil', compact('user')))->name('profil');

// Pages protégées (authentification requise)
Route::middleware(['auth', 'activite'])->group(function () {

    // ── Amis ──
    Route::get   ('/amis',                  [AmiController::class, 'index'])   ->name('amis.index');
    Route::post  ('/amis/envoyer',          [AmiController::class, 'envoyer']) ->name('amis.envoyer');
    Route::patch ('/amis/{ami}/accepter',   [AmiController::class, 'accepter'])->name('amis.accepter');
    Route::delete('/amis/{ami}/refuser',    [AmiController::class, 'refuser']) ->name('amis.refuser');
    Route::delete('/amis/{user}/retirer',   [AmiController::class, 'retirer']) ->name('amis.retirer');

});
