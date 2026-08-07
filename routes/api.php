<?php

use Hybridcore\Vote\Http\Controllers\Api\CallbackController;
use Illuminate\Support\Facades\Route;

// Postback endpoint listing sites call to confirm a vote. Authenticated by the
// per-site callback token, not a user session. Throttled to blunt abuse.
Route::match(['get', 'post'], '/vote/callback/{slug}', CallbackController::class)
    ->middleware('throttle:60,1')
    ->name('api.vote.callback');
