<?php

return [
    'nav' => 'Гласуване',
    'settings' => 'Настройки на гласуването',
    'widget_title' => 'Гласове',
    'widget_label' => 'този месец',

    // Public page
    'title' => 'Гласувай за нас',
    'subtitle' => 'Подкрепи общността с глас — и печели награди.',
    'vote_now' => 'Гласувай сега',
    'come_back' => 'Върни се',
    'reward' => ':points точки',
    'login_to_earn' => 'Влез, за да следиш гласовете си и да печелиш награди.',
    'top_voters' => 'Топ гласуващи',
    'this_month' => 'Този месец',
    'all_time' => 'Всички времена',
    'your_votes' => 'Твои гласове',
    'your_points' => 'Твои точки',
    'no_sites' => 'Все още няма настроени сайтове за гласуване.',

    // Account tab
    'account_tab' => 'Моите гласове',
    'account_subtitle' => 'Твоите награди и история на гласуване.',
    'history' => 'История на гласуване',
    'no_history' => 'Още не си гласувал.',

    // Notification
    'notif_confirmed' => 'Гласът ти е записан — спечели :points точки!',

    // Activity feed
    'feed_voted' => 'гласува за общността',

    // Onboarding
    'onboarding_title' => 'Гласувай и печели награди',
    'onboarding_body' => 'Подкрепи общността, като гласуваш за сървърите ни в сайтовете за листинг — и печели точки и in-game награди всеки път.',

    // Digest
    'digest_top' => 'Топ гласувал за месеца: :name (:votes гласа)',

    // Admin flashes
    'site_created' => 'Сайтът за гласуване е създаден.',
    'site_updated' => 'Сайтът за гласуване е обновен.',
    'site_deleted' => 'Сайтът за гласуване е изтрит.',
    'token_rotated' => 'Callback токенът е подновен.',

    // Admin dashboard
    'admin_title' => 'Гласуване',
    'admin_subtitle' => 'Сайтове за гласуване, награди и класация на топ гласуващите.',
    'manage_sites' => 'Управление на сайтове',
    'settings_link' => 'Настройки',
    'stat_sites' => 'Сайтове за гласуване',
    'stat_active_sites' => 'Активни сайтове',
    'stat_votes_today' => 'Гласове днес',
    'stat_votes_month' => 'Гласове този месец',
    'recent_votes' => 'Скорошни гласове',
    'no_votes' => 'Още няма гласове.',
    'voted_on' => 'гласува в',
    'reward_delivered' => 'Наградата е доставена',
    'reward_pending' => 'Наградата чака доставка',
    'top_voters_month' => 'Топ гласуващи този месец',

    // Admin — Sites
    'sites_breadcrumb' => 'Сайтове',
    'add_site' => 'Добави сайт',
    'no_sites_admin' => 'Още няма сайтове за гласуване. Добави един, за да започнеш.',
    'off' => 'изкл.',
    'reward_badge' => 'награда',
    'sites_pts' => ':points точки',
    'sites_votes_cooldown' => ':votes гласа · :hours ч',
    'rotate_token_title' => 'Подновяване на callback токена',
    'edit' => 'Редактирай',
    'delete' => 'Изтрий',
    'confirm_delete_site' => 'Изтрий ":name"? Гласовете му също ще бъдат премахнати.',
    'confirm_rotate_token' => 'Подновяване на callback токена за ":name"? Текущият спира да работи.',
    'edit_site_title' => 'Редактирай сайт',
    'add_site_title' => 'Добави сайт за гласуване',
    'field_name' => 'Име',
    'field_vote_url' => 'URL за гласуване',
    'field_icon' => 'URL на икона',
    'field_icon_optional' => '— по избор',
    'field_reward_points' => 'Точки за награда',
    'field_cooldown_hours' => 'Изчакване (часове)',
    'field_reward_command' => 'In-game команда за награда',
    'field_reward_command_optional' => '— по избор, доставя се през bridge-а',
    'field_placeholders' => 'Плейсхолдъри:',
    'field_active_hint' => 'Активен (показва се на публичната страница за гласуване)',
    'saving' => 'Запазване…',
    'save_changes' => 'Запази промените',

    // Admin — Settings
    'settings_breadcrumb' => 'Настройки',
    'settings_title' => 'Настройки на гласуването',
    'settings_subtitle' => 'Как работят наградите и callback-ите.',
    'defaults_heading' => 'Стойности по подразбиране',
    'default_cooldown_label' => 'Изчакване по подразбиране',
    'history_retention_label' => 'Съхранение на историята',
    'config_note' => 'Задават се в :path на разширението.',
    'callback_heading' => 'Callback за гласуване (postback)',
    'callback_body' => 'Настрой всеки сайт за листинг да вика callback URL-а ти при гласуване. Всеки сайт има собствен URL и таен токен (виж Управление на сайтове). Играчът се разпознава по :steamid (свързан Steam акаунт) или :username.',
    'rewards_heading' => 'In-game награди',
    'rewards_body' => 'Задай команда за награда на сайт, за да доставяш in-game награда през game-server bridge-а при всеки потвърден глас. Изпълнява се на всички bridge-enabled сървъри.',
];
