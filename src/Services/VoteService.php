<?php

namespace Hybridcore\Vote\Services;

use App\Models\ConnectedAccount;
use App\Models\Server;
use App\Models\User;
use App\Services\Bridge\BridgeService;
use App\Services\Extensions\Registries\HookRegistry;
use Carbon\CarbonImmutable;
use Hybridcore\Vote\Models\Vote;
use Hybridcore\Vote\Models\VoteSite;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VoteService
{
    public function __construct(private readonly BridgeService $bridge) {}

    // ── Cooldown ─────────────────────────────────────────────────

    public function lastVote(User $user, VoteSite $site): ?Vote
    {
        return Vote::where('user_id', $user->id)
            ->where('vote_site_id', $site->id)
            ->latest('created_at')
            ->first();
    }

    public function canVote(User $user, VoteSite $site): bool
    {
        return $this->nextVoteAt($user, $site) === null;
    }

    /** When the user may vote on this site again, or null if they can now. */
    public function nextVoteAt(User $user, VoteSite $site): ?CarbonImmutable
    {
        $last = $this->lastVote($user, $site);

        if ($last === null) {
            return null;
        }

        $next = CarbonImmutable::parse($last->created_at)->addHours($site->cooldown_hours);

        return $next->isFuture() ? $next : null;
    }

    // ── Recording a confirmed vote ───────────────────────────────

    /**
     * Record a confirmed vote and grant its reward. Returns null if the user
     * is still within the cooldown window (idempotent against double callbacks).
     */
    public function record(User $user, VoteSite $site, ?string $ip = null, ?Server $server = null): ?Vote
    {
        return DB::transaction(function () use ($user, $site, $ip, $server) {
            if (! $this->canVote($user, $site)) {
                return null;
            }

            $vote = Vote::create([
                'user_id' => $user->id,
                'vote_site_id' => $site->id,
                'server_id' => $server?->id,
                'ip_address' => $ip,
                'points_awarded' => $site->reward_points,
                'reward_delivered' => false,
                'created_at' => now(),
            ]);

            $this->grantReward($user, $site, $vote);

            app(HookRegistry::class)->fire('vote.confirmed', $vote);

            return $vote;
        });
    }

    /**
     * Deliver the in-game reward command (if any) to every bridge-enabled
     * server, substituting {steamid}, {name} and {points} placeholders.
     */
    private function grantReward(User $user, VoteSite $site, Vote $vote): void
    {
        if (empty($site->reward_command)) {
            return;
        }

        $steamId = ConnectedAccount::where('user_id', $user->id)
            ->where('provider', 'steam')
            ->value('provider_user_id') ?? '';

        $command = strtr($site->reward_command, [
            '{steamid}' => $steamId,
            '{name}' => $user->username ?? $user->name,
            '{points}' => (string) $site->reward_points,
        ]);

        $servers = Server::where('bridge_enabled', true)->where('is_active', true)->get();

        foreach ($servers as $server) {
            try {
                $this->bridge->queue($server, $command, config('ext.vote.bridge_source', 'hybridcore/vote'));
            } catch (\Throwable) {
                // A malformed command for one server must not block the vote.
            }
        }

        $vote->update(['reward_delivered' => true]);
    }

    // ── Leaderboard & stats ──────────────────────────────────────

    /**
     * Top voters for a period: 'month' (current calendar month) or 'all'.
     *
     * @return Collection<int, array{user: User, votes: int, points: int}>
     */
    public function topVoters(string $period = 'month', int $limit = 10): Collection
    {
        $query = Vote::query()
            ->selectRaw('user_id, COUNT(*) as votes, SUM(points_awarded) as points')
            ->groupBy('user_id')
            ->orderByDesc('votes')
            ->limit($limit);

        if ($period === 'month') {
            $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month);
        }

        $rows = $query->get();
        $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

        return $rows
            ->filter(fn ($row) => $users->has($row->user_id))
            ->map(fn ($row) => [
                'user' => $users[$row->user_id],
                'votes' => (int) $row->votes,
                'points' => (int) $row->points,
            ])
            ->values();
    }

    public function userPoints(User $user): int
    {
        return (int) Vote::where('user_id', $user->id)->sum('points_awarded');
    }

    public function userVoteCount(User $user): int
    {
        return Vote::where('user_id', $user->id)->count();
    }

    /**
     * A user's most recent votes with the site name, newest first.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function userRecentVotes(User $user, int $limit = 20): Collection
    {
        return Vote::where('user_id', $user->id)
            ->with('site:id,name')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->map(fn (Vote $vote) => [
                'id' => $vote->id,
                'site' => $vote->site?->name ?? '—',
                'points' => (int) $vote->points_awarded,
                'at' => $vote->created_at?->toIso8601String(),
            ]);
    }
}
