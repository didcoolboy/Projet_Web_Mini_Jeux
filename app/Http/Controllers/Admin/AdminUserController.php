<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameRequest;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function ban(User $user)
    {
        $user->update(['status' => 'banned']);
        return back()->with('success', "Compte {$user->nom} banni.");
    }

    public function unban(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "Compte {$user->nom} rétabli.");
    }

    public function promote(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cet utilisateur est déjà admin.');
        }
        $user->update(['role' => 'admin']);
        return back()->with('success', "{$user->pseudo} est maintenant admin.");
    }

    public function demote(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas vous rétrograder vous-même.');
        }
        if ($user->role !== 'admin') {
            return back()->with('error', 'Seuls les admins peuvent être rétrogradés.');
        }
        $user->update(['role' => 'joueur']);
        return back()->with('success', "{$user->pseudo} rétrogradé en joueur.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Impossible de supprimer votre propre compte.');
        }
        $user->delete();
        return back()->with('success', 'Compte supprimé.');
    }


    public function dashboard()
    {
        $totalUsers     = User::count();
        $activeGames     = Game::count();
        $pendingRequests = GameRequest::where('status', 'pending')->count();
        $totalRequests   = GameRequest::count();
        $bannedUsers     = User::where('status', 'banned')->count();

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentBans  = User::where('status', 'banned')->orderBy('updated_at', 'desc')->take(5)->get();
    
        return view('admin.dashboard', compact(
            'totalUsers',
            'activeGames',
            'pendingRequests',
            'totalRequests',
            'bannedUsers',
            'recentUsers',
            'recentBans'
        ));
    }

}