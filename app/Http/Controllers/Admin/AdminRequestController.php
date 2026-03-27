<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameRequest;
use App\Models\Game;

class AdminRequestController extends Controller
{
    public function index()
    {
        $requests = GameRequest::with('user')->latest()->paginate(20);
        $pendingCount = GameRequest::where('status', 'pending')->count();
        return view('admin.requests.index', compact('requests', 'pendingCount'));
    }

    public function approve(GameRequest $gameRequest)
    {
        // Crée le jeu depuis la demande
        Game::create([
            'name'     => $gameRequest->game_name,
            'category' => $gameRequest->category,
            'description' => $gameRequest->description,
        ]);

        $gameRequest->update(['status' => 'approved']);
        return back()->with('success', "\"{$gameRequest->game_name}\" approuvé et ajouté à la bibliothèque.");
    }

    public function reject(GameRequest $gameRequest)
    {
        $gameRequest->update(['status' => 'rejected']);
        return back()->with('success', 'Demande refusée.');
    }
}