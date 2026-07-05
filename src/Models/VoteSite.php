<?php

namespace Hybridcore\Vote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoteSite extends Model
{
    protected $fillable = [
        'name', 'slug', 'vote_url', 'icon', 'reward_points',
        'reward_command', 'cooldown_hours', 'callback_token', 'is_active', 'sort',
    ];

    protected function casts(): array
    {
        return [
            'reward_points' => 'integer',
            'cooldown_hours' => 'integer',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    /** Hide the callback secret from any serialization. */
    protected $hidden = ['callback_token'];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
