<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game; // adapte selon ton model existant
use Illuminate\Http\Request;

class AdminGameController extends Controller
{
    public function index()
    {
        $games = Game::latest()->paginate(20);
        return view('admin.games.index', compact('games'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        Game::create($data);
        return back()->with('success', 'Jeu ajouté avec succès.');
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return back()->with('success', 'Jeu retiré.');
    }
}