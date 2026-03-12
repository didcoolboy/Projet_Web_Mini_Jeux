<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::get('/mot-de-passe-oublie', function () {
    return view('auth.mot_de_passe_oublie');
})->name('mot_de_passe_oublie');

Route::post('/deconnexion', [AuthController::class, 'deconnexion'])->name('deconnexion');