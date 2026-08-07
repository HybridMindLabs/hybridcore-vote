<?php

namespace Hybridcore\Vote\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Hybridcore\Vote\Models\VoteSite;
use Hybridcore\Vote\Services\VoteService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class VoteController extends Controller
{
    public function __construct(private readonly VoteService $votes) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $sites = VoteSite::where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get()
            ->map(function (VoteSite $site) use ($user) {
                $nextAt = $user ? $this->votes->nextVoteAt($user, $site) : null;

                return [
                    'id' => $site->id,
                    'name' => $site->name,
                    'slug' => $site->slug,
                    'vote_url' => $site->vote_url,
                    'icon' => $site->icon,
                    'reward_points' => $site->reward_points,
                    'can_vote' => $user ? $nextAt === null : null,
                    'next_vote_at' => $nextAt?->toIso8601String(),
                ];
            });

        return Inertia::render('Extensions/hybridcore/vote/Web/Index', [
            'sites' => $sites,
            'topVoters' => [
                'month' => $this->formatLeaders($this->votes->topVoters('month')),
                'all' => $this->formatLeaders($this->votes->topVoters('all')),
            ],
            'me' => $user ? [
                'points' => $this->votes->userPoints($user),
                'votes' => $this->votes->userVoteCount($user),
            ] : null,
        ]);
    }

    /** @param Collection<int, array<string, mixed>> $leaders */
    private function formatLeaders($leaders): array
    {
        return $leaders->map(fn (array $row) => [
            'username' => $row['user']->username ?? $row['user']->name,
            'avatar' => $row['user']->avatar,
            'votes' => $row['votes'],
            'points' => $row['points'],
        ])->all();
    }
}
