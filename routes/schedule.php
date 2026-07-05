<?php

use Hybridcore\Vote\Models\Vote;
use Illuminate\Support\Facades\Schedule;

// Prune vote history older than the configured retention window so the table
// stays small on busy communities. Loaded in console context only.
Schedule::call(function (): void {
    $days = (int) config('ext.vote.history_retention_days', 180);
    Vote::where('created_at', '<', now()->subDays($days))->delete();
})->daily()->name('vote-prune-history');
