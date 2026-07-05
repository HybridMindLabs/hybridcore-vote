<?php

use Hybridcore\Vote\Http\Controllers\Account\VoteController as AccountVoteController;
use Hybridcore\Vote\Http\Controllers\Web\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/vote', [VoteController::class, 'index'])->name('vote.index');

// Account panel tab — registered with the SDK's AccountTabRegistry.
Route::middleware('auth')
    ->get('/account/votes', [AccountVoteController::class, 'index'])
    ->name('account.vote.index');
