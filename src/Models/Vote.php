<?php

namespace Hybridcore\Vote\Models;

use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    // Only created_at is used (a vote is an immutable event).
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'vote_site_id', 'server_id',
        'ip_address', 'points_awarded', 'reward_delivered',
    ];

    protected function casts(): array
    {
        return [
            'points_awarded' => 'integer',
            'reward_delivered' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(VoteSite::class, 'vote_site_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }
}
