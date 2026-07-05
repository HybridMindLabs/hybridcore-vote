<?php

namespace Hybridcore\Vote;

use App\Services\Extensions\Registries\ExtensionRegistry;
use App\Support\Slots;
use Hybridcore\Vote\Models\Vote;
use Hybridcore\Vote\Models\VoteSite;
use Hybridcore\Vote\Notifications\VoteConfirmedNotification;
use Hybridcore\Vote\Services\VoteService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class VoteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(VoteService::class);
    }

    public function boot(ExtensionRegistry $registry): void
    {
        // ── Permissions ──────────────────────────────────────────
        $registry->permissions()->register('vote.view', 'View Voting', 'vote');
        $registry->permissions()->register('vote.manage', 'Manage Voting', 'vote');

        // ── Admin navigation ─────────────────────────────────────
        $registry->navigation()->register(
            label: 'vote::messages.nav',
            route: 'admin.vote.index',
            icon: 'ThumbsUp',
            section: 'navigation.sections.content',
            permission: 'vote.view',
            sort: 60,
        );

        // ── Public (site header) navigation ──────────────────────
        $registry->publicNavigation()->register(
            label: 'vote::messages.nav',
            route: 'vote.index',
            icon: 'ThumbsUp',
            sort: 60,
        );

        // ── User dropdown link ───────────────────────────────────
        $registry->userMenu()->register(
            label: 'vote::messages.nav',
            route: 'vote.index',
            icon: 'ThumbsUp',
            sort: 60,
        );

        // ── Footer link ──────────────────────────────────────────
        $registry->footerLinks()->register(
            label: 'vote::messages.nav',
            route: 'vote.index',
            sort: 60,
        );

        // ── Admin command-palette action ─────────────────────────
        $registry->quickActions()->register(
            label: 'vote::messages.settings',
            route: 'admin.vote.settings',
            icon: 'ThumbsUp',
            permission: 'vote.manage',
            keywords: ['vote', 'voting', 'rewards'],
        );

        // ── Notification type + trigger ──────────────────────────
        $registry->notificationTypes()->register(
            type: 'vote.confirmed',
            icon: 'ThumbsUp',
            accent: 'emerald',
        );

        // On a confirmed vote, notify the voter in-app.
        $registry->hooks()->listen('vote.confirmed', function (Vote $vote) {
            $vote->user?->notify(new VoteConfirmedNotification($vote));
        });

        // ── Onboarding step (only when a site is active) ─────────
        $registry->onboardingSteps()->register(
            key: 'vote_intro',
            component: 'HybridcoreVoteOnboarding',
            title: 'vote::messages.onboarding_title',
            icon: 'ThumbsUp',
            sort: 80,
            when: fn () => VoteSite::where('is_active', true)->exists(),
        );

        // ── Weekly digest row: this month's top voter ────────────
        $registry->scheduledReports()->register(function () {
            $top = app(VoteService::class)->topVoters('month', 1)->first();

            if ($top === null) {
                return [];
            }

            return [[
                'text' => trans('vote::messages.digest_top', [
                    'name' => $top['user']->username ?? $top['user']->name,
                    'votes' => $top['votes'],
                ]),
                'url' => route('vote.index'),
            ]];
        });

        // ── Community activity feed ──────────────────────────────
        $registry->activityFeed()->register(
            type: 'vote.confirmed',
            resolver: fn (int $limit) => Vote::with('user:id,username,display_name,avatar')
                ->latest('created_at')
                ->limit($limit)
                ->get()
                ->map(fn (Vote $vote) => [
                    'username' => $vote->user?->username,
                    'avatar' => $vote->user?->avatar,
                    'text' => trans('vote::messages.feed_voted'),
                    'url' => route('vote.index'),
                    'at' => $vote->created_at,
                ])->all(),
        );

        // ── Account panel tab ────────────────────────────────────
        $registry->accountTabs()->register(
            key: 'votes',
            label: 'vote::messages.account_tab',
            route: 'account.vote.index',
            icon: 'ThumbsUp',
            sort: 60,
        );

        // ── Public profile panel ─────────────────────────────────
        $registry->profileTabs()->register(
            key: 'votes',
            label: 'vote::messages.top_voters',
            component: 'HybridcoreVoteProfilePanel',
            data: fn ($user) => [
                'points' => app(VoteService::class)->userPoints($user),
                'count' => app(VoteService::class)->userVoteCount($user),
            ],
            icon: 'Trophy',
            sort: 50,
        );

        // ── Global search: vote sites ────────────────────────────
        $registry->search()->register(
            key: 'vote_sites',
            label: 'vote::messages.nav',
            icon: 'ThumbsUp',
            resolver: fn (string $term, int $limit) => VoteSite::query()
                ->where('is_active', true)
                ->where('name', 'like', '%'.$term.'%')
                ->orderBy('sort')
                ->limit($limit)
                ->get()
                ->map(fn ($site) => [
                    'title' => $site->name,
                    'url' => route('vote.index'),
                    'meta' => trans('vote::messages.reward', ['points' => $site->reward_points]),
                ])->all(),
        );

        // ── Dashboard widget: votes this month ───────────────────
        $registry->widgets()->register(
            id: 'vote.month',
            title: 'vote::messages.widget_title',
            component: 'stat',
            data: fn () => [
                'value' => $this->safeCount(),
                'icon' => 'ThumbsUp',
                'accent' => 'accent',
                'label' => trans('vote::messages.widget_label'),
            ],
            permission: 'vote.view',
            sort: 60,
        );

        // ── Settings page link ───────────────────────────────────
        $registry->settings()->register(
            slug: 'vote',
            label: 'vote::messages.settings',
            route: 'admin.vote.settings',
            permission: 'vote.manage',
        );

        // ── Top voters on the home sidebar ───────────────────────
        $registry->slots()->register(
            Slots::HOME_RIGHT_BOTTOM,
            'HybridcoreVoteTopVoters',
            fn () => [
                'voters' => collect(app(VoteService::class)->topVoters('month', 5))
                    ->map(fn (array $r) => [
                        'username' => $r['user']->username ?? $r['user']->name,
                        'avatar' => $r['user']->avatar,
                        'votes' => $r['votes'],
                    ])->all(),
            ],
            priority: 50,
        );
    }

    /** Votes this month, resilient to the table not existing yet. */
    private function safeCount(): int
    {
        try {
            if (! Schema::hasTable('votes')) {
                return 0;
            }

            return Vote::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count();
        } catch (\Throwable) {
            return 0;
        }
    }
}
