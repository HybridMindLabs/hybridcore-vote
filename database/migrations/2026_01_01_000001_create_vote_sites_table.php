<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_sites', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('vote_url');           // where the player goes to vote
            $table->string('icon')->nullable();   // small logo URL
            $table->unsignedInteger('reward_points')->default(0);
            // Optional in-game command delivered through the bridge on a
            // confirmed vote. Supports {steamid}, {name}, {points} placeholders.
            $table->string('reward_command', 500)->nullable();
            $table->unsignedSmallInteger('cooldown_hours')->default(12);
            // Secret used to authenticate the listing site's callback/postback.
            $table->string('callback_token', 64)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_sites');
    }
};
