@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <span class="card-title">GESTION DES COMPTES</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>PSEUDO</th><th>NOM</th><th>EMAIL</th><th>RÔLE</th><th>INSCRIT LE</th><th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->pseudo }}</td>
                <td style="color:#4a6a4a">{{ $user->prenom }} {{ $user->nom }}</td>
                <td style="color:#4a6a4a">{{ $user->email }}</td>
                <td><span class="pill pill-{{ $user->role }}">{{ strtoupper($user->role) }}</span></td>
                <td style="color:#4a6a4a">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</td>
                <td>
                    <div class="actions">
                        @if($user->role === 'user')
                            <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-warn">PROMOUVOIR</button>
                            </form>
                        @elseif($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-ghost">RÉTROGRADER</button>
                            </form>
                        @endif

                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Supprimer {{ $user->pseudo }} ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-red">SUPPR.</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="empty-state">Aucun utilisateur</div></td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection