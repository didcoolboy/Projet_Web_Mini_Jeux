@extends('layouts.admin')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <span class="page-title" style="margin:0">BIBLIOTHEQUE DE JEUX</span>
    <button class="btn btn-green btn-lg" onclick="document.getElementById('add-game-form').style.display='block';this.style.display='none'">
        + AJOUTER UN JEU
    </button>
</div>

<div id="add-game-form" style="display:none" class="card" >
    <div class="card-header"><span class="card-title">NOUVEAU JEU</span></div>
    <div style="padding:16px">
        <form method="POST" action="{{ route('admin.games.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <label class="form-label">NOM DU JEU</label>
                <input class="form-input" name="name" required placeholder="Ex: Cyber Quest 2049">
            </div>
            <div class="form-row">
                <label class="form-label">DESCRIPTION</label>
                <textarea class="form-textarea" name="description" placeholder="Courte description..."></textarea>
            </div>
            <div class="form-row">
                <label class="form-label">FICHIER ARCHIVE DU JEU (.ZIP OU .RAR)</label>
                <input class="form-input" type="file" name="zip_file" accept=".zip,.rar" required>
                <small style="display:block;margin-top:8px;color:#9aa4b2;">
                    L'archive doit contenir <strong>index.html</strong> (ou <strong>jeux.html</strong>) a la racine. Les fichiers serveur/executables sont refuses.
                </small>
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
        <div class="game-card-meta">Slug: {{ $game->slug }}</div>
        <div class="actions">
            <a class="btn btn-ghost" href="{{ route('jeux.dynamic', $game->slug) }}" target="_blank" rel="noopener">JOUER</a>
            <form method="POST" action="{{ route('admin.games.destroy', $game) }}"
                  onsubmit="return confirm('Retirer {{ $game->name }} ?')">
                @csrf @method('DELETE')
                <button class="btn btn-red">RETIRER</button>
            </form>
        </div>
    </div>
    @empty
        <div class="empty-state" style="grid-column:1/-1">Aucun jeu enregistre.</div>
    @endforelse
</div>
@endsection
