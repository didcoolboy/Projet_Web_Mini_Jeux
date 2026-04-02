@extends('layouts.admin')

@section('content')
<p class="page-title">DEMANDES D'AJOUT DE JEUX — {{ $pendingCount }} EN ATTENTE</p>

@forelse($requests as $req)
<div class="request-card">
    <div class="request-card-header">
        <div>
            <div class="request-game">{{ $req->game_name }}</div>
            <div class="request-meta">
                Catégorie : {{ $req->category }} &bull;
                Par <strong style="color:#c8e6c8">{{ $req->user->pseudo }}</strong> &bull;
                {{ $req->created_at->format('d/m/Y') }}
            </div>
        </div>
        <span class="pill pill-{{ $req->status }}">{{ strtoupper($req->status) }}</span>
    </div>

    @if($req->description)
        <div class="request-desc">{{ $req->description }}</div>
    @endif

    @if($req->status === 'pending')
    <div class="actions">
        <form method="POST" action="{{ route('admin.requests.approve', $req) }}">
            @csrf @method('PATCH')
            <button class="btn btn-green">APPROUVER ET AJOUTER</button>
        </form>
        <form method="POST" action="{{ route('admin.requests.reject', $req) }}">
            @csrf @method('PATCH')
            <button class="btn btn-red">REFUSER</button>
        </form>
    </div>
    @endif
</div>
@empty
    <div class="empty-state">Aucune demande pour le moment.</div>
@endforelse

<div class="pagination">{{ $requests->links() }}</div>
@endsection