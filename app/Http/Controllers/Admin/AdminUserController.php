<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return back()->with('success', "Compte {$user->name} banni.");
    }

    public function unban(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "Compte {$user->name} rétabli.");
    }

    public function promote(User $user)
    {
        $user->update(['role' => 'admin']);
        return back()->with('success', "{$user->name} est maintenant admin.");
    }

    public function demote(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas vous rétrograder vous-même.');
        }
        $user->update(['role' => 'user']);
        return back()->with('success', "{$user->name} rétrogradé.");
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
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentBans  = User::where('status', 'banned')->orderBy('updated_at', 'desc')->take(5)->get();
    
        return view('admin.dashboard', compact('recentUsers', 'recentBans'));
    }

}