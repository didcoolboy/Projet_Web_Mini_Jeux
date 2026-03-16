<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Affiche le classement global ou par jeu.
     *
     * GET /classement?game=snake&filter=friends
     */
    public function index(Request $request)
    {
        $gameSlug = $request->query('game', 'all');
        $filter   = $request->query('filter', 'all'); // all | friends
        $search   = $request->query('search', '');

        /** @var User $me */
        $me    = Auth::user();
        $games = Game::orderBy('id')->get();

        // ─── IDs des amis de l'utilisateur connecté ───────────
        $friendIds = $this->getFriendIds($me->id);

        // ─── Construction du classement ───────────────────────
        $leaderboard = $this->buildLeaderboard(
            me: $me,
            games: $games,
            gameSlug: $gameSlug,
            filter: $filter,
            friendIds: $friendIds,
            search: $search,
        );

        // ─── Stats personnelles ───────────────────────────────
        $myStats = $this->getMyStats($me, $games, $friendIds);

        return view('leaderboard.index', [
            'leaderboard' => $leaderboard,
            'games'       => $games,
            'activeGame'  => $gameSlug,
            'filter'      => $filter,
            'search'      => $search,
            'myStats'     => $myStats,
            'friendIds'   => $friendIds,
            'myId'        => $me->id,
        ]);
    }

    // ─── Helpers privés ───────────────────────────────────────

    /**
     * Retourne les IDs des amis acceptés de l'utilisateur.
     */
    private function getFriendIds(int $userId): array
    {
        return DB::table('friendships')
            ->whereNotNull('accepted_at')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->get()
            ->map(fn($f) => $f->sender_id === $userId ? $f->receiver_id : $f->sender_id)
            ->toArray();
    }

    /**
     * Construit le tableau de classement.
     */
    private function buildLeaderboard(
        User   $me,
        object $games,
        string $gameSlug,
        string $filter,
        array  $friendIds,
        string $search,
    ): array {
        // Base : score total ou score d'un jeu précis
        if ($gameSlug === 'all') {
            $query = DB::table('users')
                ->leftJoin('scores', 'users.id', '=', 'scores.user_id')
                ->select('users.id', 'users.name', DB::raw('COALESCE(SUM(scores.score), 0) as total_score'))
                ->groupBy('users.id', 'users.name');
        } else {
            $game = $games->firstWhere('slug', $gameSlug);
            $query = DB::table('users')
                ->leftJoin('scores', function ($join) use ($game) {
                    $join->on('users.id', '=', 'scores.user_id')
                         ->where('scores.game_id', '=', $game?->id ?? 0);
                })
                ->select('users.id', 'users.name', DB::raw('COALESCE(SUM(scores.score), 0) as total_score'))
                ->groupBy('users.id', 'users.name');
        }

        // Filtre amis
        if ($filter === 'friends') {
            $visibleIds = array_merge($friendIds, [$me->id]);
            $query->whereIn('users.id', $visibleIds);
        }

        // Recherche
        if ($search !== '') {
            $query->where('users.name', 'like', "%{$search}%");
        }

        $rows = $query->orderByDesc('total_score')->get();

        // Enrichir chaque ligne avec rang, isFriend, isYou
        return $rows->values()->map(function ($row, $index) use ($me, $friendIds, $games, $gameSlug) {
            $perGame = [];
            foreach ($games as $g) {
                $perGame[$g->slug] = (int) DB::table('scores')
                    ->where('user_id', $row->id)
                    ->where('game_id', $g->id)
                    ->sum('score');
            }

            return [
                'rank'        => $index + 1,
                'id'          => $row->id,
                'name'        => $row->name,
                'total_score' => (int) $row->total_score,
                'per_game'    => $perGame,
                'is_you'      => $row->id === $me->id,
                'is_friend'   => in_array($row->id, $friendIds),
            ];
        })->toArray();
    }

    /**
     * Calcule les stats personnelles du joueur connecté.
     */
    private function getMyStats(User $me, object $games, array $friendIds): array
    {
        $totalScore = (int) DB::table('scores')->where('user_id', $me->id)->sum('score');

        // Rang global
        $rank = DB::table('users')
            ->leftJoin('scores', 'users.id', '=', 'scores.user_id')
            ->select('users.id', DB::raw('COALESCE(SUM(scores.score), 0) as total_score'))
            ->groupBy('users.id')
            ->orderByDesc('total_score')
            ->get()
            ->search(fn($r) => $r->id === $me->id);

        return [
            'total_score'  => $totalScore,
            'global_rank'  => $rank !== false ? $rank + 1 : '?',
            'friends_count'=> count($friendIds),
            'games_count'  => $games->count(),
        ];
    }
}
