@extends('layouts.admin')

@section('content')
<h1>Demandes d'ajout de jeux ({{ $pendingCount }} en attente)</h1>

@foreach($requests as $req)
<div class="request-card">
    <div>
        <strong>{{ $req->game_name }}</strong>
        <span>{{ $req->category }}</span>
        <span>par {{ $req->user->name }} le {{ $req->created_at->format('d/m/Y') }}</span>
        <span class="pill pill-{{ $req->status }}">{{ strtoupper($req->status) }}</span>
    </div>
    <p>{{ $req->description }}</p>

    @if($req->status === 'pending')
    <div>
        <form method="POST" action="{{ route('admin.requests.approve', $req) }}">
            @csrf @method('PATCH')
            <button class="btn-green">Approuver et ajouter</button>
        </form>
        <form method="POST" action="{{ route('admin.requests.reject', $req) }}">
            @csrf @method('PATCH')
            <button class="btn-red">Refuser</button>
        </form>
    </div>
    @endif
</div>
@endforeach

{{ $requests->links() }}
@endsection