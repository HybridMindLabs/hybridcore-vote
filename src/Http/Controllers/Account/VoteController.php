<?php

namespace Hybridcore\Vote\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Hybridcore\Vote\Services\VoteService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VoteController extends Controller
{
    public function __construct(private readonly VoteService $votes) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Extensions/hybridcore/vote/Account/Votes', [
            'points' => $this->votes->userPoints($user),
            'count' => $this->votes->userVoteCount($user),
            'recent' => $this->votes->userRecentVotes($user, 30),
        ]);
    }
}
