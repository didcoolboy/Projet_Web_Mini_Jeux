@extends('layouts.admin')

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">UTILISATEURS</div>
        <div class="stat-value green">{{ number_format($totalUsers, 0, ',', ' ') }}</div>
        <div class="stat-sub">comptes enregistrés</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">JEUX ACTIFS</div>
        <div class="stat-value green">{{ number_format($activeGames, 0, ',', ' ') }}</div>
        <div class="stat-sub">{{ $pendingRequests }} en attente</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">DEMANDES</div>
        <div class="stat-value orange">{{ number_format($totalRequests, 0, ',', ' ') }}</div>
        <div class="stat-sub">{{ $pendingRequests }} à traiter</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">BANNIS</div>
        <div class="stat-value red">{{ number_format($bannedUsers, 0, ',', ' ') }}</div>
        <div class="stat-sub">comptes suspendus</div>
    </div>
</div>

{{-- Activité récente --}}
<div class="activity-card">
    <h2>ACTIVITÉ RÉCENTE</h2>
    <table>
        <thead>
            <tr>
                <th>DATE</th>
                <th>ÉVÉNEMENT</th>
                <th>UTILISATEUR</th>
                <th>STATUT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentUsers as $user)
            <tr>
                <td>{{ $user->created_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                <td>Nouveau compte</td>
                <td>{{ $user->pseudo }}</td>
                <td><span class="badge badge-active">ACTIF</span></td>
            </tr>
            @endforeach

            @foreach($recentBans as $user)
            <tr>
                <td>{{ $user->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                <td>Compte banni</td>
                <td>{{ $user->pseudo }}</td>
                <td><span class="badge badge-banned">BANNI</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection