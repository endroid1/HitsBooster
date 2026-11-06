<?php

define('LANG_HITSBOOSTER_TITLE', 'HitsBooster');
define('LANG_HITSBOOSTER_DESCRIPTION', 'Автоматическое плавное увеличение просмотров опубликованных материалов');
define('LANG_HITSBOOSTER_CP_HELP', 'Инструкция и описание');

define('LANG_HITSBOOSTER_OPT_PARAMS', 'Параметры накрутки просмотров');
define('LANG_HITSBOOSTER_OPT_CTYPES', 'Типы контента для обработки');
define('LANG_HITSBOOSTER_OPT_MIN_STEP', 'Минимальная прибавка за раз');
define('LANG_HITSBOOSTER_OPT_MAX_STEP', 'Максимальная прибавка за раз');
define('LANG_HITSBOOSTER_OPT_MAX_HITS', 'Останавливать при достижении просмотров');
define('LANG_HITSBOOSTER_OPT_MAX_HITS_HINT', 'Материалы, у которых число просмотров >= этому значению, исключаются из накрутки');
define('LANG_HITSBOOSTER_OPT_MIN_ORGANIC', 'Порог старта (минимальные органические просмотры)');
define('LANG_HITSBOOSTER_OPT_MIN_ORGANIC_HINT', 'Материал начнет накручиваться только после достижения указанного числа реальных просмотров (0 — накручивать сразу)');
define('LANG_HITSBOOSTER_OPT_DAYS_LIMIT', 'Возраст записей (дней)');
define('LANG_HITSBOOSTER_OPT_DAYS_LIMIT_HINT', 'Обрабатывать материалы, опубликованные не ранее указанного срока (0 — без ограничений)');
define('LANG_HITSBOOSTER_OPT_BATCH_LIMIT', 'Размер выборки за один запуск (LIMIT)');
define('LANG_HITSBOOSTER_OPT_BATCH_LIMIT_HINT', 'Число случайно выбираемых записей для снижения нагрузки на БД');
define('LANG_HITSBOOSTER_OPT_USE_DIURNAL', 'Использовать суточный коэффициент активности');
define('LANG_HITSBOOSTER_OPT_USE_DIURNAL_HINT', 'Снижает прирост ночью (01:00 - 06:59) и увеличивает в дневной и вечерний прайм-тайм');
define('LANG_HITSBOOSTER_OPT_DISABLE_NIGHT', 'Полная пауза в ночное время');
define('LANG_HITSBOOSTER_OPT_DISABLE_NIGHT_HINT', 'Отключать выполнение накрутки с 02:00 до 06:00');

define('LANG_HITSBOOSTER_OPT_CURVE_GROUP', 'Суточная кривая активности (множители)');
define('LANG_HITSBOOSTER_OPT_CURVE_NIGHT', '02:00 — 05:59 (Глубокая ночь)');
define('LANG_HITSBOOSTER_OPT_CURVE_MORNING', '06:00 — 08:59 (Утренний подъем)');
define('LANG_HITSBOOSTER_OPT_CURVE_DAY', '09:00 — 17:59 (Рабочее время)');
define('LANG_HITSBOOSTER_OPT_CURVE_EVENING', '18:00 — 22:59 (Вечерний пик)');
define('LANG_HITSBOOSTER_OPT_CURVE_LATE', '23:00 — 01:59 (Поздний вечер)');

define('LANG_HITSBOOSTER_TASK_TITLE', 'HitsBooster: Накрутка просмотров контента');
define('LANG_HITSBOOSTER_PACKAGE_CONTENTS', 'Файлы пакета');
define('LANG_HITSBOOSTER_CRON_MANAGE', 'Управление планировщиком');

define('LANG_HITSBOOSTER_HELP_ABOUT_TITLE', 'О компоненте HitsBooster');
define('LANG_HITSBOOSTER_HELP_ABOUT_TEXT', 'Компонент HitsBooster разработан для естественного и плавного увеличения счетчиков просмотров материалов контента на сайте без создания дополнительной нагрузки на пользователей и веб-сервер.');
define('LANG_HITSBOOSTER_HELP_SETTINGS_TITLE', 'Подробное описание параметров');
define('LANG_HITSBOOSTER_HELP_DIURNAL_TITLE', 'Суточная кривая активности (множители)');
define('LANG_HITSBOOSTER_HELP_CRON_TITLE', 'Настройка планировщика (CRON)');
define('LANG_HITSBOOSTER_HELP_CRON_HINT', 'Все операции выполняются исключительно в фоновом режиме через планировщик задач InstantCMS.');
