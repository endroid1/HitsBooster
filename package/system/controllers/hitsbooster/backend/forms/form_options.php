<?php

class formHitsboosterOptions extends cmsForm {

    public function init() {
        return [
            [
                'type' => 'fieldset',
                'title' => defined('LANG_HITSBOOSTER_OPT_PARAMS') ? LANG_HITSBOOSTER_OPT_PARAMS : 'Параметры накрутки просмотров',
                'childs' => [
                    new fieldListMultiple('ctypes', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_CTYPES') ? LANG_HITSBOOSTER_OPT_CTYPES : 'Типы контента для обработки',
                        'show_all' => false,
                        'generator' => function() {
                            $model = cmsCore::getModel('content');
                            $ctypes = $model->getContentTypes();
                            $items = [];
                            foreach ($ctypes as $ctype) {
                                $items[$ctype['name']] = $ctype['title'];
                            }
                            return $items;
                        }
                    ]),
                    new fieldNumber('min_step', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_MIN_STEP') ? LANG_HITSBOOSTER_OPT_MIN_STEP : 'Минимальная прибавка за раз',
                        'default' => 1,
                        'rules' => [['min', 1]]
                    ]),
                    new fieldNumber('max_step', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_MAX_STEP') ? LANG_HITSBOOSTER_OPT_MAX_STEP : 'Максимальная прибавка за раз',
                        'default' => 5,
                        'rules' => [['min', 1]]
                    ]),
                    new fieldNumber('max_hits', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_MAX_HITS') ? LANG_HITSBOOSTER_OPT_MAX_HITS : 'Останавливать при достижении просмотров',
                        'hint' => defined('LANG_HITSBOOSTER_OPT_MAX_HITS_HINT') ? LANG_HITSBOOSTER_OPT_MAX_HITS_HINT : 'Материалы, у которых hits >= этому значению, исключаются из накрутки',
                        'default' => 1000
                    ]),
                    new fieldNumber('min_organic_hits', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_MIN_ORGANIC') ? LANG_HITSBOOSTER_OPT_MIN_ORGANIC : 'Порог старта (минимальные органические просмотры)',
                        'hint' => defined('LANG_HITSBOOSTER_OPT_MIN_ORGANIC_HINT') ? LANG_HITSBOOSTER_OPT_MIN_ORGANIC_HINT : 'Материал начнет накручиваться только после достижения указанного числа реальных просмотров (0 — накручивать сразу)',
                        'default' => 0
                    ]),
                    new fieldNumber('days_limit', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_DAYS_LIMIT') ? LANG_HITSBOOSTER_OPT_DAYS_LIMIT : 'Возраст записей (дней)',
                        'hint' => defined('LANG_HITSBOOSTER_OPT_DAYS_LIMIT_HINT') ? LANG_HITSBOOSTER_OPT_DAYS_LIMIT_HINT : 'Обрабатывать материалы, опубликованные не ранее указанного срока (0 — без ограничений)',
                        'default' => 7
                    ]),
                    new fieldNumber('batch_limit', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_BATCH_LIMIT') ? LANG_HITSBOOSTER_OPT_BATCH_LIMIT : 'Размер выборки за один запуск (LIMIT)',
                        'hint' => defined('LANG_HITSBOOSTER_OPT_BATCH_LIMIT_HINT') ? LANG_HITSBOOSTER_OPT_BATCH_LIMIT_HINT : 'Число случайно выбираемых записей для снижения нагрузки на БД',
                        'default' => 15
                    ]),
                    new fieldCheckbox('use_diurnal_curve', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_USE_DIURNAL') ? LANG_HITSBOOSTER_OPT_USE_DIURNAL : 'Использовать суточный коэффициент активности',
                        'hint' => defined('LANG_HITSBOOSTER_OPT_USE_DIURNAL_HINT') ? LANG_HITSBOOSTER_OPT_USE_DIURNAL_HINT : 'Снижает прирост ночью (01:00 - 06:59) и увеличивает в дневной и вечерний прайм-тайм',
                        'default' => 1
                    ]),
                    new fieldCheckbox('disable_at_night', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_DISABLE_NIGHT') ? LANG_HITSBOOSTER_OPT_DISABLE_NIGHT : 'Полная пауза в ночное время',
                        'hint' => defined('LANG_HITSBOOSTER_OPT_DISABLE_NIGHT_HINT') ? LANG_HITSBOOSTER_OPT_DISABLE_NIGHT_HINT : 'Отключать выполнение накрутки с 02:00 до 06:00',
                        'default' => 0
                    ])
                ]
            ],
            [
                'type' => 'fieldset',
                'title' => defined('LANG_HITSBOOSTER_OPT_CURVE_GROUP') ? LANG_HITSBOOSTER_OPT_CURVE_GROUP : 'Суточная кривая активности (множители)',
                'childs' => [
                    new fieldNumber('curve_night', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_CURVE_NIGHT') ? LANG_HITSBOOSTER_OPT_CURVE_NIGHT : '02:00 — 05:59 (Глубокая ночь)',
                        'default' => 0.15,
                        'options' => ['is_ceil' => false, 'decimal_int' => 2],
                        'rules' => [['min', 0], ['max', 10]]
                    ]),
                    new fieldNumber('curve_morning', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_CURVE_MORNING') ? LANG_HITSBOOSTER_OPT_CURVE_MORNING : '06:00 — 08:59 (Утренний подъем)',
                        'default' => 0.60,
                        'options' => ['is_ceil' => false, 'decimal_int' => 2],
                        'rules' => [['min', 0], ['max', 10]]
                    ]),
                    new fieldNumber('curve_day', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_CURVE_DAY') ? LANG_HITSBOOSTER_OPT_CURVE_DAY : '09:00 — 17:59 (Рабочее время)',
                        'default' => 1.00,
                        'options' => ['is_ceil' => false, 'decimal_int' => 2],
                        'rules' => [['min', 0], ['max', 10]]
                    ]),
                    new fieldNumber('curve_evening', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_CURVE_EVENING') ? LANG_HITSBOOSTER_OPT_CURVE_EVENING : '18:00 — 22:59 (Вечерний пик)',
                        'default' => 1.35,
                        'options' => ['is_ceil' => false, 'decimal_int' => 2],
                        'rules' => [['min', 0], ['max', 10]]
                    ]),
                    new fieldNumber('curve_late', [
                        'title' => defined('LANG_HITSBOOSTER_OPT_CURVE_LATE') ? LANG_HITSBOOSTER_OPT_CURVE_LATE : '23:00 — 01:59 (Поздний вечер)',
                        'default' => 0.40,
                        'options' => ['is_ceil' => false, 'decimal_int' => 2],
                        'rules' => [['min', 0], ['max', 10]]
                    ])
                ]
            ]
        ];
    }
}
