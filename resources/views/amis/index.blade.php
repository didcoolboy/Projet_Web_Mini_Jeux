@extends('layouts.pixel')

@section('title', 'Amis')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/amis.css') }}">
  <style>
    .pixels {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
    }
  </style>
@endpush

@section('content')

  {{-- Pixels flottants --}}
  <div class="pixels" id="pixels"></div>

  {{-- Titre + envoi de demande --}}
  <div class="page-header">
    <div class="page-title">
      <small>★ SOCIAL ★</small>
      AMIS
    </div>
  </div>

  {{-- Onglets --}}
  <div class="tabs">
    <div class="tab active" onclick="switchTab('mes-amis', this)">
      MES AMIS <span style="color:var(--muted);font-size:.65rem;margin-left:6px;">({{ $amis->count() }})</span>
    </div>
    <div class="tab" onclick="switchTab('demandes', this)">
      DEMANDES
      @if($demandes->count() > 0)
        <span class="badge">{{ $demandes->count() }}</span>
      @endif
    </div>
    <div class="tab" onclick="switchTab('classement', this)">CLASSEMENT</div>
  </div>

  {{-- ── Section : Mes amis ── --}}
  <div class="section active" id="mes-amis">
    <div class="friends-grid" id="friendsGrid">
      @forelse($amis as $ami)
        @php
          $index = $loop->index;
          $rank  = $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : null));
          $rankNames = ['gold' => 'OR', 'silver' => 'ARG', 'bronze' => 'BRZ'];
          $amiScore   = $ami->totalScore();
          $amiParties = \App\Models\Score::where('user_id', $ami->id)->count();
        @endphp
        <div class="friend-card {{ $rank ? 'rank-'.$rank : '' }}">

          @if($rank === 'gold')
            <svg class="crown-pixel" viewBox="0 0 7 5" xmlns="http://www.w3.org/2000/svg" width="28" height="20">
              <rect x="0" y="0" width="1" height="1" fill="#fff5a0"/>
              <rect x="3" y="0" width="1" height="1" fill="#ffffff"/>
              <rect x="6" y="0" width="1" height="1" fill="#fff5a0"/>
              <rect x="0" y="1" width="1" height="1" fill="#ffe600"/>
              <rect x="1" y="1" width="1" height="1" fill="#ffd000"/>
              <rect x="2" y="1" width="1" height="1" fill="#ffe600"/>
              <rect x="3" y="1" width="1" height="1" fill="#ffe600"/>
              <rect x="4" y="1" width="1" height="1" fill="#ffe600"/>
              <rect x="5" y="1" width="1" height="1" fill="#ffd000"/>
              <rect x="6" y="1" width="1" height="1" fill="#ffe600"/>
              <rect x="0" y="2" width="7" height="1" fill="#ffb700"/>
              <rect x="0" y="3" width="7" height="1" fill="#ffd000"/>
              <rect x="1" y="3" width="1" height="1" fill="#ff3366"/>
              <rect x="3" y="3" width="1" height="1" fill="#00d4ff"/>
              <rect x="5" y="3" width="1" height="1" fill="#ff3366"/>
              <rect x="0" y="4" width="7" height="1" fill="#ffb700"/>
            </svg>
          @endif

          <div class="card-top">
            <div class="avatar">
              {{ strtoupper(substr($ami->pseudo, 0, 2)) }}
              <div class="status-dot offline"></div>
            </div>
            <div class="card-info">
              <div class="card-pseudo">
                {{ $ami->pseudo }}
                @if($rank)
                  <span class="rank-label {{ $rank }}">{{ $rankNames[$rank] }}</span>
                @endif
              </div>
            </div>
          </div>

          <div class="card-stats">
            <div class="stat-box">
              <div class="stat-label">Score</div>
              <div class="stat-value {{ $rank ?? '' }}">{{ number_format($amiScore, 0, ',', ' ') }}</div>
            </div>
            <div class="stat-box">
              <div class="stat-label">Parties</div>
              <div class="stat-value">{{ $amiParties }}</div>
            </div>
          </div>

          <div class="card-actions">
            <form method="POST" action="{{ route('amis.retirer', $ami->id) }}" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">RETIRER</button>
            </form>
            <a href="{{ route('profil', $ami->id) }}" class="btn btn-sm">PROFIL</a>
          </div>

        </div>
      @empty
        <div style="color:var(--muted);font-size:.72rem;letter-spacing:2px;padding:20px 0;">
          AUCUN AMI POUR L'INSTANT
        </div>
      @endforelse
    </div>
  </div>

  {{-- ── Section : Demandes ── --}}
  <div class="section" id="demandes">

    <div class="send-request-block">
      <div class="section-label">ENVOYER UNE DEMANDE D'AMI</div>
      <form method="POST" action="{{ route('amis.envoyer') }}" class="send-request-row" id="sendReqForm">
        @csrf
        <input type="text" name="pseudo" id="sendReqInput" placeholder="PSEUDO DU JOUEUR..." autocomplete="off" class="req-input">
        <button type="submit" class="btn">+ ENVOYER</button>
      </form>
      @if(session('req_error'))
        <div class="send-req-result" style="color:var(--accent2);border-color:var(--accent2);">✕ {{ session('req_error') }}</div>
      @elseif(session('req_success'))
        <div class="send-req-result" style="color:var(--accent);border-color:var(--accent);">▶ {{ session('req_success') }}</div>
      @endif
    </div>

    <div class="section-label">DEMANDES REÇUES</div>
    <div class="request-list" id="requestList">
      @forelse($demandes as $demande)
        <div class="request-card" id="demande-{{ $demande->id }}">
          <div class="avatar">
            {{ strtoupper(substr($demande->sender->pseudo, 0, 2)) }}
            <div class="status-dot offline"></div>
          </div>
          <div class="req-info">
            <div class="req-pseudo">{{ $demande->sender->pseudo }}</div>
            <div class="req-time">
              Demande reçue {{ $demande->created_at->diffForHumans() }}
              · score : {{ number_format($demande->sender->totalScore(), 0, ',', ' ') }}
            </div>
          </div>
          <div class="req-actions">
            <form method="POST" action="{{ route('amis.accepter', $demande->id) }}" style="display:inline;">
              @csrf
              @method('PATCH')
              <button type="submit" class="btn btn-sm">✓ ACCEPTER</button>
            </form>
            <form method="POST" action="{{ route('amis.refuser', $demande->id) }}" style="display:inline;">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger">✕ REFUSER</button>
            </form>
          </div>
        </div>
      @empty
        <div style="color:var(--muted);font-size:.72rem;letter-spacing:2px;padding:20px 0;">AUCUNE DEMANDE EN ATTENTE</div>
      @endforelse
    </div>
  </div>

  {{-- ── Section : Classement ── --}}
  <div class="section" id="classement">
    <div class="rank-table-wrapper">
      <table class="rank-table">
        <thead>
          <tr>
            <th>#</th>
            <th>PSEUDO</th>
            <th class="text-right">SCORE</th>
            <th class="text-right">PARTIES</th>
          </tr>
        </thead>
        <tbody>
          @foreach($classement as $index => $joueur)
            @php
              $rp = $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : null));
              $rankColors = ['gold' => '#ffe600', 'silver' => '#b0bec5', 'bronze' => '#cd7c4b'];
              $medals     = ['gold' => '🥇', 'silver' => '🥈', 'bronze' => '🥉'];
              $rankNames2 = ['gold' => 'OR', 'silver' => 'ARG', 'bronze' => 'BRZ'];
              $isMe       = $joueur->id === auth()->id();
              $jScore     = $joueur->totalScore();
              $jParties   = \App\Models\Score::where('user_id', $joueur->id)->count();
            @endphp
            <tr style="border-bottom:1px solid var(--border);{{ $rp ? 'border-left:3px solid '.$rankColors[$rp].';' : '' }}{{ $isMe ? 'background:rgba(0,255,136,0.04);' : '' }}">
              <td style="padding:14px 16px;font-size:.8rem;font-family:'Orbitron',sans-serif;color:{{ $rp ? $rankColors[$rp] : 'var(--muted)' }};">
                {{ $rp ? $medals[$rp] : '#'.($index + 1) }}
              </td>
              <td style="padding:14px 16px;font-family:'Orbitron',sans-serif;font-size:.78rem;font-weight:700;color:{{ $isMe ? 'var(--accent)' : 'var(--text)' }};">
                {{ $joueur->pseudo }}
                @if($isMe) <span style="font-size:.6rem;color:var(--accent);letter-spacing:2px;"> (VOUS)</span> @endif
                @if($rp) <span style="font-size:.55rem;border:1px solid {{ $rankColors[$rp] }};color:{{ $rankColors[$rp] }};padding:1px 5px;margin-left:6px;letter-spacing:2px;">{{ $rankNames2[$rp] }}</span> @endif
              </td>
              <td style="padding:14px 16px;text-align:right;font-family:'Orbitron',sans-serif;font-size:.85rem;font-weight:700;color:{{ $rp ? $rankColors[$rp] : 'var(--accent)' }};">
                {{ number_format($jScore, 0, ',', ' ') }}
              </td>
              <td style="padding:14px 16px;text-align:right;font-size:.78rem;color:var(--muted);">{{ $jParties }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="{{ asset('js/amis.js') }}"></script>
  <script>
    // Assurer que switchTab fonctionne
    if (typeof switchTab === 'undefined') {
      function switchTab(id, el) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        el.classList.add('active');
      }
    }

    // Pixels flottants - APPARITION CONTINUE
    document.addEventListener('DOMContentLoaded', () => {
      const pixelsEl = document.getElementById('pixels');
      if (pixelsEl) {
        const bgCols = ['#00ff88', '#ff3366', '#ffdd00', '#bf00ff', '#00d4ff'];
        let pixelCounter = 0;

        // Créer un nouveau pixel toutes les 200-400ms
        setInterval(() => {
          const p = document.createElement('div');
          p.className = 'pixel';
          const size = 2 + Math.floor(Math.random() * 4);
          const speed = 5 + Math.random() * 8; // VITESSE MOYENNE entre 5 et 13 secondes
          const animName = `floatPixel${pixelCounter++}`; // ANIMATION UNIQUE pour chaque pixel

          // Créer une animation CSS unique pour ce pixel
          const style = document.createElement('style');
          style.textContent = `
            @keyframes ${animName} {
              0%   { transform: translateY(0); opacity: 0; }
              10%  { opacity: 1; }
              90%  { opacity: 1; }
              100% { transform: translateY(-120vh); opacity: 0; }
            }
          `;
          document.head.appendChild(style);

          p.style.cssText = `position:absolute;left:${Math.random()*100}%;top:100vh;background:${bgCols[Math.floor(Math.random()*bgCols.length)]};width:${size}px;height:${size}px;animation:${animName} ${speed}s linear infinite;`;
          pixelsEl.appendChild(p);

          // Nettoyer les pixels terminés après 15 secondes
          setTimeout(() => {
            if (p.parentNode) {
              p.parentNode.removeChild(p);
            }
            if (style.parentNode) {
              style.parentNode.removeChild(style);
            }
          }, speed * 1000 + 1000);
        }, 200 + Math.random() * 200); // INTERVALLE ALÉATOIRE entre 200 et 400ms
      }
    });
  </script>
@endpush
