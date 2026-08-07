<?php

return [
    'nav' => 'Voting',
    'settings' => 'Voting Settings',
    'widget_title' => 'Votes',
    'widget_label' => 'this month',

    // Public page
    'title' => 'Vote for us',
    'subtitle' => 'Support the community by voting — and earn rewards.',
    'vote_now' => 'Vote now',
    'come_back' => 'Come back',
    'reward' => ':points points',
    'login_to_earn' => 'Log in to track your votes and earn rewards.',
    'top_voters' => 'Top voters',
    'this_month' => 'This month',
    'all_time' => 'All time',
    'your_votes' => 'Your votes',
    'your_points' => 'Your points',
    'no_sites' => 'No vote sites configured yet.',

    // Account tab
    'account_tab' => 'My votes',
    'account_subtitle' => 'Your voting rewards and history.',
    'history' => 'Vote history',
    'no_history' => "You haven't voted yet.",

    // Notification
    'notif_confirmed' => 'Your vote was recorded — you earned :points points!',

    // Activity feed
    'feed_voted' => 'voted for the community',

    // Onboarding
    'onboarding_title' => 'Vote & earn rewards',
    'onboarding_body' => 'Support the community by voting for our servers on listing sites — and earn points and in-game rewards every time.',

    // Digest
    'digest_top' => "This month's top voter: :name (:votes votes)",

    // Admin flashes
    'site_created' => 'Vote site created.',
    'site_updated' => 'Vote site updated.',
    'site_deleted' => 'Vote site deleted.',
    'token_rotated' => 'Callback token rotated.',

    // Admin dashboard
    'admin_title' => 'Voting',
    'admin_subtitle' => 'Vote sites, rewards and the top-voters leaderboard.',
    'manage_sites' => 'Manage sites',
    'settings_link' => 'Settings',
    'stat_sites' => 'Vote sites',
    'stat_active_sites' => 'Active sites',
    'stat_votes_today' => 'Votes today',
    'stat_votes_month' => 'Votes this month',
    'recent_votes' => 'Recent votes',
    'no_votes' => 'No votes yet.',
    'voted_on' => 'voted on',
    'reward_delivered' => 'Reward delivered',
    'reward_pending' => 'Reward pending',
    'top_voters_month' => 'Top voters this month',

    // Admin — Sites
    'sites_breadcrumb' => 'Sites',
    'add_site' => 'Add site',
    'no_sites_admin' => 'No vote sites yet. Add one to get started.',
    'off' => 'off',
    'reward_badge' => 'reward',
    'sites_pts' => ':points pts',
    'sites_votes_cooldown' => ':votes votes · :hours h',
    'rotate_token_title' => 'Rotate callback token',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'confirm_delete_site' => 'Delete ":name"? Its votes will be removed too.',
    'confirm_rotate_token' => 'Rotate the callback token for ":name"? The current one stops working.',
    'edit_site_title' => 'Edit site',
    'add_site_title' => 'Add vote site',
    'field_name' => 'Name',
    'field_vote_url' => 'Vote URL',
    'field_icon' => 'Icon URL',
    'field_icon_optional' => '— optional',
    'field_reward_points' => 'Reward points',
    'field_cooldown_hours' => 'Cooldown (hours)',
    'field_reward_command' => 'In-game reward command',
    'field_reward_command_optional' => '— optional, delivered via the bridge',
    'field_placeholders' => 'Placeholders:',
    'field_active_hint' => 'Active (shown on the public vote page)',
    'saving' => 'Saving…',
    'save_changes' => 'Save changes',

    // Admin — Settings
    'settings_breadcrumb' => 'Settings',
    'settings_title' => 'Voting settings',
    'settings_subtitle' => 'How rewards and callbacks work.',
    'defaults_heading' => 'Defaults',
    'default_cooldown_label' => 'Default cooldown',
    'history_retention_label' => 'Vote history retention',
    'config_note' => "These are set in the extension's :path.",
    'callback_heading' => 'Vote callback (postback)',
    'callback_body' => 'Configure each listing site to call your callback URL when a player votes. Each site has its own URL and secret token (see Manage sites). The player is matched by :steamid (a linked Steam account) or :username.',
    'rewards_heading' => 'In-game rewards',
    'rewards_body' => 'Set a reward command on a site to deliver an in-game reward through the game-server bridge on every confirmed vote. It runs on all bridge-enabled servers.',
];
