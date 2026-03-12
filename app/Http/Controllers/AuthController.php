<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showConnexion()
    {
        return view('auth.connexion');
    }

    public function connexion(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();
            return redirect()->route('accueil');
        }

        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect.',
        ])->withInput();
    }

    public function showInscription()
    {
        return view('auth.inscription');
    }

    public function inscription(Request $request)
{
    $request->validate([
        'nom'      => 'required|string|max:50',
        'prenom'   => 'required|string|max:50',
        'email'    => 'required|email|unique:users',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'nom'      => $request->nom,
        'prenom'   => $request->prenom,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'joueur',
    ]);

    Auth::login($user);

    return redirect()->route('accueil');
}

    public function deconnexion(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }
}