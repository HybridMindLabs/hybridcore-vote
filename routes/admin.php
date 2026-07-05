<?php

use Hybridcore\Vote\Http\Controllers\Admin\SettingsController;
use Hybridcore\Vote\Http\Controllers\Admin\VoteController;
use Hybridcore\Vote\Http\Controllers\Admin\VoteSiteController;
use Illuminate\Support\Facades\Route;

// Loaded with web + auth + admin middleware and the /admin prefix by the core.
Route::middleware('perm:vote.view')->group(function (): void {
    Route::get('/vote', [VoteController::class, 'index'])->name('admin.vote.index');
    Route::get('/vote/sites', [VoteSiteController::class, 'index'])->name('admin.vote.sites.index');
});

Route::middleware('perm:vote.manage')->group(function (): void {
    Route::post('/vote/sites', [VoteSiteController::class, 'store'])->name('admin.vote.sites.store');
    Route::put('/vote/sites/{site}', [VoteSiteController::class, 'update'])->name('admin.vote.sites.update');
    Route::delete('/vote/sites/{site}', [VoteSiteController::class, 'destroy'])->name('admin.vote.sites.destroy');
    Route::post('/vote/sites/{site}/rotate-token', [VoteSiteController::class, 'rotateToken'])->name('admin.vote.sites.rotate-token');

    Route::get('/vote/settings', [SettingsController::class, 'index'])->name('admin.vote.settings');
});
