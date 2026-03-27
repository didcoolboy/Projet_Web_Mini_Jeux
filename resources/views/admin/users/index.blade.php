@extends('layouts.admin')

@section('content')
<table>
    <thead>
        <tr>
            <th>Pseudo</th><th>Email</th><th>Rôle</th>
            <th>Statut</th><th>Inscrit le</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->status ?? 'active' }}</td>
            <td>{{ $user->created_at->format('d/m/Y') }}</td>
            <td>
                @if(($user->status ?? 'active') === 'active')
                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                        @csrf @method('PATCH')
                        <button>Bannir</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                        @csrf @method('PATCH')
                        <button>Rétablir</button>
                    </form>
                @endif

                @if($user->role === 'user')
                    <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                        @csrf @method('PATCH')
                        <button>Promouvoir admin</button>
                    </form>
                @elseif($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                        @csrf @method('PATCH')
                        <button>Rétrograder</button>
                    </form>
                @endif

                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                          onsubmit="return confirm('Supprimer ce compte ?')">
                        @csrf @method('DELETE')
                        <button>Supprimer</button>
                    </form>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $users->links() }}
@endsection