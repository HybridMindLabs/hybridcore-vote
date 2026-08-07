<?php

namespace Hybridcore\Vote\Tests\Feature;

use App\Http\Middleware\EnsureIsAdmin;
use App\Models\ConnectedAccount;
use App\Models\User;
use App\Services\Extensions\ExtensionAutoloader;
use Hybridcore\Vote\Models\VoteSite;
use Hybridcore\Vote\VoteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class VoteAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        file_put_contents(storage_path('installed.lock'), 'installed');

        // Routes normally register only for extensions the DB marks enabled
        // at boot — a fresh RefreshDatabase test has no such row, so wire
        // them by hand. Mirrors the staff/announcements/servercast tests.
        $base = base_path('extensions/hybridcore/vote');
        ExtensionAutoloader::register($base, ['namespace' => 'Hybridcore\\Vote']);

        $this->app->register(VoteServiceProvider::class);

        Route::middleware(['web', 'auth', EnsureIsAdmin::class])
            ->prefix('admin')->group($base.'/routes/admin.php');
        Route::middleware('api')->prefix('api')->group($base.'/routes/api.php');

        $this->app['router']->getRoutes()->refreshNameLookups();
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
            'callback_token' => 'secret-token',
            'is_active' => true,
        ], $attributes));
    }

    public function test_sites_index_requires_the_view_permission(): void
    {
        $this->get(route('admin.vote.sites.index'))->assertRedirect();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.vote.sites.index'))->assertOk();
    }

    public function test_admin_can_create_a_site_with_an_auto_generated_slug_and_token(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post(route('admin.vote.sites.store'), [
            'name' => 'TopG', 'vote_url' => 'https://topg.org/', 'reward_points' => 10, 'cooldown_hours' => 12,
        ])->assertRedirect();

        $site = VoteSite::where('name', 'TopG')->firstOrFail();
        $this->assertSame('topg', $site->slug);
        $this->assertNotEmpty($site->callback_token);
    }

    public function test_a_second_site_with_the_same_name_gets_a_disambiguated_slug(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->site(['name' => 'TopG', 'slug' => 'topg']);

        $this->actingAs($admin)->post(route('admin.vote.sites.store'), [
            'name' => 'TopG', 'vote_url' => 'https://topg.org/2', 'reward_points' => 5, 'cooldown_hours' => 6,
        ])->assertRedirect();

        $this->assertDatabaseHas('vote_sites', ['vote_url' => 'https://topg.org/2', 'slug' => 'topg-2']);
    }

    public function test_admin_can_rotate_a_sites_callback_token(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $site = $this->site(['callback_token' => 'old-token']);

        $this->actingAs($admin)->post(route('admin.vote.sites.rotate-token', $site))->assertRedirect();

        $this->assertNotSame('old-token', $site->fresh()->callback_token);
    }

    public function test_admin_can_delete_a_site(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $site = $this->site();

        $this->actingAs($admin)->delete(route('admin.vote.sites.destroy', $site))->assertRedirect();

        $this->assertDatabaseMissing('vote_sites', ['id' => $site->id]);
    }

    public function test_managing_sites_requires_the_manage_permission(): void
    {
        $guest = User::factory()->create(['is_admin' => false]);

        $this->actingAs($guest)->post(route('admin.vote.sites.store'), [
            'name' => 'TopG', 'vote_url' => 'https://topg.org/', 'reward_points' => 10, 'cooldown_hours' => 12,
        ])->assertRedirect('/admin/login');
    }

    // ── Callback ─────────────────────────────────────────────────

    public function test_callback_rejects_an_invalid_token(): void
    {
        $site = $this->site(['callback_token' => 'right-token']);

        $this->postJson(route('api.vote.callback', $site->slug), [
            'token' => 'wrong-token', 'username' => 'someone',
        ])->assertStatus(403);
    }

    public function test_callback_returns_404_for_an_unknown_site(): void
    {
        $this->postJson(route('api.vote.callback', 'no-such-site'), [
            'token' => 'x',
        ])->assertStatus(404);
    }

    public function test_callback_records_a_vote_by_username(): void
    {
        $user = User::factory()->create(['username' => 'player1']);
        $site = $this->site(['callback_token' => 'right-token']);

        $response = $this->postJson(route('api.vote.callback', $site->slug), [
            'token' => 'right-token', 'username' => 'player1',
        ]);

        $response->assertOk()->assertJson(['status' => 'recorded', 'points' => 10]);
        $this->assertDatabaseHas('votes', ['user_id' => $user->id, 'vote_site_id' => $site->id]);
    }

    public function test_callback_resolves_by_linked_steam_id(): void
    {
        $user = User::factory()->create();
        ConnectedAccount::create([
            'user_id' => $user->id, 'provider' => 'steam', 'provider_user_id' => 'STEAM_0:1:1',
        ]);
        $site = $this->site(['callback_token' => 'right-token']);

        $this->postJson(route('api.vote.callback', $site->slug), [
            'token' => 'right-token', 'steamid' => 'STEAM_0:1:1',
        ])->assertOk()->assertJson(['status' => 'recorded']);
    }

    public function test_callback_reports_cooldown_without_double_recording(): void
    {
        $user = User::factory()->create(['username' => 'player1']);
        $site = $this->site(['callback_token' => 'right-token', 'cooldown_hours' => 12]);

        $this->postJson(route('api.vote.callback', $site->slug), [
            'token' => 'right-token', 'username' => 'player1',
        ])->assertJson(['status' => 'recorded']);

        $this->postJson(route('api.vote.callback', $site->slug), [
            'token' => 'right-token', 'username' => 'player1',
        ])->assertOk()->assertJson(['status' => 'cooldown']);

        $this->assertSame(1, \Hybridcore\Vote\Models\Vote::where('user_id', $user->id)->count());
    }
}
