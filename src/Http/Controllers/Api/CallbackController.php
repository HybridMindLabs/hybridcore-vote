<?php

namespace Hybridcore\Vote\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConnectedAccount;
use App\Models\User;
use Hybridcore\Vote\Models\VoteSite;
use Hybridcore\Vote\Services\VoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Postback endpoint that server-listing sites call to confirm a player's vote.
 *
 *   POST/GET /api/vote/callback/{slug}
 *     token=<site callback_token>
 *     steamid=<STEAM_0:...>   (or)  username=<site account name>
 *
 * The token authenticates the listing site; the identifier resolves the player.
 */
class CallbackController extends Controller
{
    public function __construct(private readonly VoteService $votes) {}

    public function __invoke(Request $request, string $slug): JsonResponse
    {
        $site = VoteSite::where('slug', $slug)->where('is_active', true)->first();

        if ($site === null) {
            return response()->json(['error' => 'Unknown vote site.'], 404);
        }

        // Constant-time token check (skip only when no token is configured).
        $token = (string) $request->input('token', '');
        if (! empty($site->callback_token) && ! hash_equals($site->callback_token, $token)) {
            return response()->json(['error' => 'Invalid token.'], 403);
        }

        $user = $this->resolveUser($request);

        if ($user === null) {
            return response()->json(['error' => 'Player not found.'], 404);
        }

        $vote = $this->votes->record($user, $site, $request->ip());

        // A null result means the player is still within the cooldown window —
        // report success so the listing site does not keep retrying.
        return response()->json([
            'status' => $vote ? 'recorded' : 'cooldown',
            'points' => $vote?->points_awarded ?? 0,
        ]);
    }

    private function resolveUser(Request $request): ?User
    {
        if ($steamId = $request->input('steamid')) {
            $userId = ConnectedAccount::where('provider', 'steam')
                ->where('provider_user_id', $steamId)
                ->value('user_id');

            if ($userId) {
                return User::find($userId);
            }
        }

        if ($username = $request->input('username')) {
            return User::where('username', $username)->first();
        }

        return null;
    }
}
