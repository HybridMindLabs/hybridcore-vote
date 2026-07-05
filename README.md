# HybridCore Vote

[![HybridCore](https://img.shields.io/badge/HybridCore-%E2%89%A5%200.3.0-6366f1)](https://github.com/HybridMindLabs/HybridCore)
[![Version](https://img.shields.io/badge/version-1.0.0-22c55e)](CHANGELOG.md)
[![License](https://img.shields.io/badge/license-Proprietary-64748b)](LICENSE)

The official **Vote** extension for [HybridCore](https://github.com/HybridMindLabs/HybridCore) — let your players vote for your servers on listing sites and reward them automatically.

- 🗳️ **Vote sites** — manage any number of listing sites (TopG, Server-List, GameTracker, …), each with its own vote URL, cooldown, and reward.
- 🎁 **Automatic rewards** — award community points on every confirmed vote, and optionally deliver an in-game reward through the HybridCore **game-server bridge**.
- 🔒 **Secure callbacks (postback)** — each site gets a unique, rotatable secret token; incoming callbacks are verified with a constant-time comparison.
- 🏆 **Top-voters leaderboard** — monthly and all-time rankings on the public page and a Home sidebar widget.
- 🌐 **Player matching** — resolves the voter by their linked **Steam** account (`steamid`) or by `username`.
- 🌍 **Localised** — ships with English and Bulgarian translations.

## Requirements

- HybridCore **≥ 0.3.0**

## Installation

### From the admin panel (recommended)

1. Download the latest `hybridcore-vote-x.y.z.zip` from [Releases](https://github.com/HybridMindLabs/hybridcore-vote/releases).
2. **Admin → Extensions → Import** and upload the ZIP.
3. Click **Enable** on *Vote*.

### Manually

```bash
# From your HybridCore root
cd extensions/hybridcore
git clone https://github.com/HybridMindLabs/hybridcore-vote.git vote

php artisan hybridcore:extensions:sync
php artisan hybridcore:extensions:enable hybridcore/vote
```

Enabling runs the extension's migrations automatically and seeds two disabled example sites you can edit or delete.

## Configuration

Defaults live in [`config/vote.php`](config/vote.php) and are exposed as `config('ext.vote.*')`:

| Key | Default | Description |
| --- | --- | --- |
| `default_cooldown_hours` | `12` | Fallback cooldown when a site doesn't set its own. |
| `history_retention_days` | `180` | Votes older than this are pruned daily. |
| `bridge_source` | `hybridcore/vote` | Source tag for bridge-delivered reward commands. |

## Setting up a vote callback (postback)

Each listing site can call your callback URL when a player votes, so rewards are granted the moment the vote is confirmed.

1. **Admin → Voting → Sites** — open a site and copy its **callback URL**. It looks like:

   ```
   https://your-community.com/api/vote/callback/{slug}?token=SECRET&steamid={steamid}
   ```

2. Paste it into the listing site's *postback / callback URL* field.
3. The site replaces `{steamid}` (or you can pass `username=`) when a player votes.

The token is verified with `hash_equals`; rotate it any time from the admin UI if it leaks.

## In-game rewards (game-server bridge)

Set a **reward command** on a site to deliver an in-game reward on every confirmed vote. It is queued to all bridge-enabled, active servers. Placeholders:

| Placeholder | Replaced with |
| --- | --- |
| `{steamid}` | The voter's linked Steam ID |
| `{name}` | The voter's username |
| `{points}` | Points awarded for this vote |

Example:

```
hc_give_points {steamid} {points}
```

## Development

This extension is developed in-place inside a HybridCore install under `extensions/hybridcore/vote/`.

```bash
# Run the extension's test suite (isolated sqlite env)
php artisan hybridcore:extensions:test hybridcore/vote

# Package a distributable ZIP
php artisan hybridcore:extensions:zip hybridcore/vote
```

See [`BUILDING_EXTENSIONS.md`](https://github.com/HybridMindLabs/HybridCore/blob/main/extensions/BUILDING_EXTENSIONS.md) in the core repository for the full Extension SDK reference.

## License

Proprietary — © HybridMind Labs. See [LICENSE](LICENSE).
