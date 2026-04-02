@extends('layouts.admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <span class="page-title" style="margin:0">BIBLIOTHÈQUE DE JEUX</span>
    <button class="btn btn-green btn-lg" onclick="document.getElementById('add-game-form').style.display='block';this.style.display='none'">
        + AJOUTER UN JEU
    </button>
</div>

<div id="add-game-form" style="display:none" class="card" >
    <div class="card-header"><span class="card-title">NOUVEAU JEU</span></div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('admin.games.store') }}">
            @csrf
            <div class="form-row">
                <label class="form-label">NOM DU JEU</label>
                <input class="form-input" name="name" required placeholder="Ex: Cyber Quest 2049">
            </div>
            <div class="form-row">
                <label class="form-label">CATÉGORIE</label>
                <select class="form-select" name="category">
                    <option>Action</option>
                    <option>RPG</option>
                    <option>FPS</option>
                    <option>Stratégie</option>
                    <option>Sport</option>
                    <option>Puzzle</option>
                </select>
            </div>
            <div class="form-row">
                <label class="form-label">DESCRIPTION</label>
                <textarea class="form-textarea" name="description" placeholder="Courte description..."></textarea>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-green">PUBLIER</button>
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('add-game-form').style.display='none';document.querySelector('.btn-green.btn-lg').style.display='inline-block'">ANNULER</button>
            </div>
        </form>
    </div>
</div>

<div class="game-grid">
    @forelse($games as $game)
    <div class="game-card">
        <div class="game-card-title">{{ $game->name }}</div>
        <div class="game-card-meta">{{ $game->category }}</div>
        <div class="actions">
            <form method="POST" action="{{ route('admin.games.destroy', $game) }}"
                  onsubmit="return confirm('Retirer {{ $game->name }} ?')">
                @csrf @method('DELETE')
                <button class="btn btn-red">RETIRER</button>
            </form>
        </div>
    </div>
    @empty
        <div class="empty-state" style="grid-column:1/-1">Aucun jeu enregistré.</div>
    @endforelse
</div>
@endsection