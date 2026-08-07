<?php

return [
    // Default hours a player must wait before voting on the same site again.
    'default_cooldown_hours' => 12,

    // How many days of vote history to keep (older rows are pruned).
    'history_retention_days' => 180,

    // Bridge source id used when queueing in-game reward commands.
    'bridge_source' => 'hybridcore/vote',
];
