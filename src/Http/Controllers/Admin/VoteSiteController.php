<?php

namespace Hybridcore\Vote\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Hybridcore\Vote\Models\VoteSite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VoteSiteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Extensions/hybridcore/vote/Admin/Sites/Index', [
            'sites' => VoteSite::withCount('votes')
                ->orderBy('sort')
                ->orderBy('name')
                ->get()
                ->map(fn (VoteSite $s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'vote_url' => $s->vote_url,
                    'icon' => $s->icon,
                    'reward_points' => $s->reward_points,
                    'cooldown_hours' => $s->cooldown_hours,
                    'is_active' => $s->is_active,
                    'votes_count' => $s->votes_count,
                    'has_command' => ! empty($s->reward_command),
                    // The callback URL the listing site should ping.
                    'callback_url' => url("/api/vote/callback/{$s->slug}"),
                    'has_token' => ! empty($s->callback_token),
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['callback_token'] = Str::random(40);

        VoteSite::create($data);

        return back()->with('success', __('vote::messages.site_created'));
    }

    public function update(Request $request, VoteSite $site): RedirectResponse
    {
        $site->update($this->validated($request));

        return back()->with('success', __('vote::messages.site_updated'));
    }

    public function destroy(VoteSite $site): RedirectResponse
    {
        $site->delete();

        return back()->with('success', __('vote::messages.site_deleted'));
    }

    /** Rotate the callback token — the old one stops working immediately. */
    public function rotateToken(VoteSite $site): RedirectResponse
    {
        $site->update(['callback_token' => Str::random(40)]);

        return back()->with('success', __('vote::messages.token_rotated'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'vote_url' => ['required', 'url', 'max:255'],
            'icon' => ['nullable', 'url', 'max:255'],
            'reward_points' => ['required', 'integer', 'min:0', 'max:100000'],
            'reward_command' => ['nullable', 'string', 'max:500'],
            'cooldown_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'is_active' => ['boolean'],
            'sort' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'site';
        $slug = $base;
        $i = 2;

        while (VoteSite::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
