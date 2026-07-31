<?php

namespace Hybridcore\Vote\Tests\Feature;

use App\Models\User;
use Hybridcore\Vote\Models\Vote;
use Hybridcore\Vote\Models\VoteSite;
use Hybridcore\Vote\Services\VoteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        file_put_contents(storage_path('installed.lock'), 'installed');
    }

    protected function tearDown(): void
    {
        @unlink(storage_path('installed.lock'));
        parent::tearDown();
    }

    private function site(array $attributes = []): VoteSite
    {
        return VoteSite::create(array_merge([
            'name' => 'TopG',
            'slug' => 'topg-'.uniqid(),
            'vote_url' => 'https://topg.org/',
            'reward_points' => 10,
            'cooldown_hours' => 12,
            'is_active' => true,
        ], $attributes));
    }

    public function test_records_a_vote_and_awards_points(): void
    {
        $user = User::factory()->create();
        $site = $this->site();

        $vote = app(VoteService::class)->record($user, $site, '1.2.3.4');

        $this->assertNotNull($vote);
        $this->assertSame(10, $vote->points_awarded);
        $this->assertSame(10, app(VoteService::class)->userPoints($user));
        $this->assertSame(1, app(VoteService::class)->userVoteCount($user));
    }

    public function test_cooldown_blocks_a_second_vote(): void
    {
        $user = User::factory()->create();
        $site = $this->site(['cooldown_hours' => 12]);
        $service = app(VoteService::class);

        $first = $service->record($user, $site);
        $second = $service->record($user, $site);

        $this->assertNotNull($first);
        $this->assertNull($second, 'A second vote within the cooldown must be rejected.');
        $this->assertSame(1, Vote::count());
        $this->assertFalse($service->canVote($user, $site));
    }

    public function test_can_vote_again_after_cooldown_passes(): void
    {
        $user = User::factory()->create();
        $site = $this->site(['cooldown_hours' => 12]);
        $service = app(VoteService::class);

        $vote = $service->record($user, $site);
        $vote->forceFill(['created_at' => now()->subHours(13)])->save();

        $this->assertTrue($service->canVote($user, $site));
        $this->assertNotNull($service->record($user, $site));
        $this->assertSame(2, Vote::count());
    }

    public function test_top_voters_ranks_by_vote_count(): void
    {
        $heavy = User::factory()->create(['username' => 'heavy']);
        $light = User::factory()->create(['username' => 'light']);
        $site = $this->site(['cooldown_hours' => 1]);

        // Heavy votes 3 times (spaced past cooldown), light once.
        foreach (range(1, 3) as $i) {
            Vote::create(['user_id' => $heavy->id, 'vote_site_id' => $site->id, 'points_awarded' => 10, 'created_at' => now()->subMinutes($i)]);
        }
        Vote::create(['user_id' => $light->id, 'vote_site_id' => $site->id, 'points_awarded' => 10, 'created_at' => now()]);

        $top = app(VoteService::class)->topVoters('month');

        $this->assertSame('heavy', $top[0]['user']->username);
        $this->assertSame(3, $top[0]['votes']);
        $this->assertSame('light', $top[1]['user']->username);
    }
}
