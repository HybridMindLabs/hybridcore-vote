<?php

namespace Hybridcore\Vote\Database\Seeders;

use Hybridcore\Vote\Models\VoteSite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds two example vote sites (disabled) so admins have a template to edit.
 * Idempotent — safe to run on every enable.
 */
class DefaultSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            ['name' => 'TopG', 'slug' => 'topg', 'vote_url' => 'https://topg.org/'],
            ['name' => 'Server-List', 'slug' => 'server-list', 'vote_url' => 'https://server-list.example/'],
        ];

        foreach ($examples as $example) {
            VoteSite::firstOrCreate(
                ['slug' => $example['slug']],
                [
                    'name' => $example['name'],
                    'vote_url' => $example['vote_url'],
                    'reward_points' => 10,
                    'cooldown_hours' => 12,
                    'callback_token' => Str::random(40),
                    'is_active' => false,
                    'sort' => 0,
                ],
            );
        }
    }
}
