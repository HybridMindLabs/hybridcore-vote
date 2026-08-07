# Changelog

All notable changes to the HybridCore Vote extension are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-07-04

### Added
- Manage any number of vote sites, each with its own vote URL, icon, cooldown, and reward.
- Automatic community-point rewards on every confirmed vote.
- Optional in-game rewards delivered through the HybridCore game-server bridge, with `{steamid}`, `{name}`, and `{points}` placeholders.
- Secure callback (postback) endpoint per site with a unique, rotatable token verified via `hash_equals`.
- Player matching by linked Steam account (`steamid`) or `username`.
- Public voting page with per-site cooldown state and a monthly / all-time top-voters leaderboard.
- Home sidebar widget showing the month's top voters.
- Admin dashboard with vote stats, recent votes, and site CRUD.
- Daily pruning of vote history older than the configured retention window.
- English and Bulgarian translations.

[1.0.0]: https://github.com/HybridMindLabs/hybridcore-vote/releases/tag/v1.0.0
