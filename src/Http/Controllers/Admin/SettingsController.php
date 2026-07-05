<?php

namespace Hybridcore\Vote\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Extensions/hybridcore/vote/Admin/Settings', [
            'config' => [
                'default_cooldown_hours' => (int) config('ext.vote.default_cooldown_hours', 12),
                'history_retention_days' => (int) config('ext.vote.history_retention_days', 180),
            ],
            'callback_example' => url('/api/vote/callback/{slug}').'?token={token}&steamid={steamid}',
        ]);
    }
}
