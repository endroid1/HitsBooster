<?php

define('LANG_HITSBOOSTER_TITLE', 'HitsBooster');
define('LANG_HITSBOOSTER_DESCRIPTION', 'Automatic smooth views counter booster for published content');
define('LANG_HITSBOOSTER_CP_HELP', 'Guide & Documentation');

define('LANG_HITSBOOSTER_OPT_PARAMS', 'View count boosting parameters');
define('LANG_HITSBOOSTER_OPT_CTYPES', 'Content types to process');
define('LANG_HITSBOOSTER_OPT_MIN_STEP', 'Minimum increment per run');
define('LANG_HITSBOOSTER_OPT_MAX_STEP', 'Maximum increment per run');
define('LANG_HITSBOOSTER_OPT_MAX_HITS', 'Stop threshold for views');
define('LANG_HITSBOOSTER_OPT_MAX_HITS_HINT', 'Items with views count >= this value will be excluded from boosting');
define('LANG_HITSBOOSTER_OPT_MIN_ORGANIC', 'Start threshold (minimum organic views)');
define('LANG_HITSBOOSTER_OPT_MIN_ORGANIC_HINT', 'Content starts boosting only after reaching this many real views (0 — boost immediately)');
define('LANG_HITSBOOSTER_OPT_DAYS_LIMIT', 'Content age (days)');
define('LANG_HITSBOOSTER_OPT_DAYS_LIMIT_HINT', 'Process materials published no earlier than this period (0 — unlimited)');
define('LANG_HITSBOOSTER_OPT_BATCH_LIMIT', 'Batch sample size per run (LIMIT)');
define('LANG_HITSBOOSTER_OPT_BATCH_LIMIT_HINT', 'Number of randomly picked records to prevent database overload');
define('LANG_HITSBOOSTER_OPT_USE_DIURNAL', 'Use diurnal activity curve');
define('LANG_HITSBOOSTER_OPT_USE_DIURNAL_HINT', 'Reduces increments at night (01:00 - 06:59) and boosts during daytime and evening peak hours');
define('LANG_HITSBOOSTER_OPT_DISABLE_NIGHT', 'Complete pause at night');
define('LANG_HITSBOOSTER_OPT_DISABLE_NIGHT_HINT', 'Disable execution from 02:00 to 06:00');

define('LANG_HITSBOOSTER_OPT_CURVE_GROUP', 'Diurnal activity curve (multipliers)');
define('LANG_HITSBOOSTER_OPT_CURVE_NIGHT', '02:00 — 05:59 (Deep night)');
define('LANG_HITSBOOSTER_OPT_CURVE_MORNING', '06:00 — 08:59 (Morning rise)');
define('LANG_HITSBOOSTER_OPT_CURVE_DAY', '09:00 — 17:59 (Daytime hours)');
define('LANG_HITSBOOSTER_OPT_CURVE_EVENING', '18:00 — 22:59 (Evening peak)');
define('LANG_HITSBOOSTER_OPT_CURVE_LATE', '23:00 — 01:59 (Late evening)');

define('LANG_HITSBOOSTER_TASK_TITLE', 'HitsBooster: Boost content views');
define('LANG_HITSBOOSTER_PACKAGE_CONTENTS', 'Package Files');
define('LANG_HITSBOOSTER_CRON_MANAGE', 'Manage scheduler tasks');

define('LANG_HITSBOOSTER_HELP_ABOUT_TITLE', 'About HitsBooster');
define('LANG_HITSBOOSTER_HELP_ABOUT_TEXT', 'HitsBooster is engineered for natural and smooth boosting of content view counters without imposing additional overhead on frontend requests.');
define('LANG_HITSBOOSTER_HELP_SETTINGS_TITLE', 'Detailed Settings Guide');
define('LANG_HITSBOOSTER_HELP_DIURNAL_TITLE', 'Diurnal Activity Curve (Multipliers)');
define('LANG_HITSBOOSTER_HELP_CRON_TITLE', 'Scheduler Setup (CRON)');
define('LANG_HITSBOOSTER_HELP_CRON_HINT', 'All updates occur exclusively in the background via the InstantCMS scheduler.');
