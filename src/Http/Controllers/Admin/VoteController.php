<?php

namespace Hybridcore\Vote\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Hybridcore\Vote\Models\Vote;
use Hybridcore\Vote\Models\VoteSite;
use Hybridcore\Vote\Services\VoteService;
use Inertia\Inertia;
use Inertia\Response;

class VoteController extends Controller
{
    public function __construct(private readonly VoteService $votes) {}

    public function index(): Response
    {
        return Inertia::render('Extensions/hybridcore/vote/Admin/Index', [
            'stats' => [
                'sites' => VoteSite::count(),
                'active_sites' => VoteSite::where('is_active', true)->count(),
                'votes_today' => Vote::whereDate('created_at', today())->count(),
                'votes_month' => Vote::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)->count(),
            ],
            'recentVotes' => Vote::with(['user:id,name,username', 'site:id,name'])
                ->latest('created_at')
                ->limit(15)
                ->get()
                ->map(fn (Vote $v) => [
                    'id' => $v->id,
                    'user' => $v->user?->username ?? $v->user?->name ?? 'unknown',
                    'site' => $v->site?->name,
                    'points' => $v->points_awarded,
                    'delivered' => $v->reward_delivered,
                    'at' => $v->created_at->diffForHumans(),
                ]),
            'topVoters' => collect($this->votes->topVoters('month'))
                ->map(fn (array $r) => [
                    'username' => $r['user']->username ?? $r['user']->name,
                    'votes' => $r['votes'],
                    'points' => $r['points'],
                ]),
        ]);
    }
}
