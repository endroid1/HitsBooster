<?php
/**
 * Шаблон страницы справки и описания компонента HitsBooster (тема modern)
 *
 * @var hitsbooster $controller
 * @var array $options
 * @var array $ctypes
 * @var array|null $scheduler_task
 */

$this->addBreadcrumb(defined('LANG_HITSBOOSTER_CP_HELP') ? LANG_HITSBOOSTER_CP_HELP : 'Инструкция и описание');

$curve_night   = isset($options['curve_night']) && is_numeric($options['curve_night']) ? (float)$options['curve_night'] : 0.15;
$curve_morning = isset($options['curve_morning']) && is_numeric($options['curve_morning']) ? (float)$options['curve_morning'] : 0.60;
$curve_day     = isset($options['curve_day']) && is_numeric($options['curve_day']) ? (float)$options['curve_day'] : 1.00;
$curve_evening = isset($options['curve_evening']) && is_numeric($options['curve_evening']) ? (float)$options['curve_evening'] : 1.35;
$curve_late    = isset($options['curve_late']) && is_numeric($options['curve_late']) ? (float)$options['curve_late'] : 0.40;

$selectedCtypes = $options['ctypes'] ?? [];
$ctypes_map = [];
if (!empty($ctypes)) {
    foreach ($ctypes as $c) {
        $ctypes_map[$c['name']] = $c['title'];
    }
}
?>

<div class="card mb-4">
    <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <span><?php echo html(LANG_HITSBOOSTER_TITLE); ?> &mdash; <?php echo html(defined('LANG_HITSBOOSTER_DESCRIPTION') ? LANG_HITSBOOSTER_DESCRIPTION : 'Автоматическое плавное увеличение просмотров'); ?></span>
        <span class="badge badge-secondary">v1.0.0</span>
    </div>
    <div class="card-body">
        <p class="mb-3">
            <?php echo html(defined('LANG_HITSBOOSTER_HELP_ABOUT_TEXT') ? LANG_HITSBOOSTER_HELP_ABOUT_TEXT : 'Компонент HitsBooster разработан для естественного и плавного увеличения счетчиков просмотров материалов контента на сайте без создания дополнительной нагрузки на пользователей и веб-сервер.'); ?>
        </p>

        <div class="row">
            <div class="col-md-4 mb-2">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="font-weight-bold mb-1">Нулевой оверхед</div>
                    <small class="text-muted">Накрутка не выполняется при посещении страниц пользователями. Вся работа выполняется планировщиком задач в фоновом режиме.</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="font-weight-bold mb-1">Атомарные SQL-запросы</div>
                    <small class="text-muted">Прямое пакетное обновление поля просмотров без тяжелой загрузки моделей и без изменения даты правки материала.</small>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="font-weight-bold mb-1">Рандомизация и безопасность</div>
                    <small class="text-muted">Случайный выбор материалов, рандомный шаг и суточная кривая устраняют шаблонность прироста.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header font-weight-bold">
                <?php echo html(defined('LANG_HITSBOOSTER_HELP_SETTINGS_TITLE') ? LANG_HITSBOOSTER_HELP_SETTINGS_TITLE : 'Подробное описание параметров'); ?>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="w-25">Параметр</th>
                            <th class="w-25">Текущее значение</th>
                            <th>Назначение и рекомендации</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Типы контента</strong><br><small class="text-muted"><code>ctypes</code></small></td>
                            <td>
                                <?php
                                $selected_titles = [];
                                if (!empty($selectedCtypes)) {
                                    foreach ($selectedCtypes as $ct) {
                                        if (!empty($ctypes_map[$ct])) {
                                            $selected_titles[] = $ctypes_map[$ct];
                                        }
                                    }
                                }
                                if ($selected_titles) {
                                    echo html(implode(', ', $selected_titles));
                                } else {
                                    echo '<span class="text-muted">' . (defined('LANG_NOT_SET') ? LANG_NOT_SET : 'Не выбрано') . '</span>';
                                }
                                ?>
                            </td>
                            <td>Разделы сайта (типы контента), опубликованные материалы которых участвуют в выборке.</td>
                        </tr>
                        <tr>
                            <td><strong>Диапазон шага</strong><br><small class="text-muted"><code>min_step</code> &mdash; <code>max_step</code></small></td>
                            <td>
                                <?php echo (int)($options['min_step'] ?? 1); ?> &mdash; <?php echo (int)($options['max_step'] ?? 5); ?>
                            </td>
                            <td>Минимальное и максимальное случайное число просмотров, прибавляемое к каждой выбранной статье за один запуск планировщика. Предотвращает одинаковый прирост.</td>
                        </tr>
                        <tr>
                            <td><strong>Потолок просмотров</strong><br><small class="text-muted"><code>max_hits</code></small></td>
                            <td><?php echo (int)($options['max_hits'] ?? 1000); ?></td>
                            <td>Материалы, у которых текущее количество просмотров уже достигло или превысило это значение, автоматически исключаются из накрутки. Защищает от аномальной статистики.</td>
                        </tr>
                        <tr>
                            <td><strong>Порог старта</strong><br><small class="text-muted"><code>min_organic_hits</code></small></td>
                            <td><?php echo (int)($options['min_organic_hits'] ?? 0); ?></td>
                            <td>Статья начнет получать просмотры только после того, как наберет указанное количество реальных (органических) просмотров от посетителей. <code>0</code> &mdash; накручивать сразу после публикации.</td>
                        </tr>
                        <tr>
                            <td><strong>Возраст записей</strong><br><small class="text-muted"><code>days_limit</code></small></td>
                            <td><?php echo (int)($options['days_limit'] ?? 7); ?> дней</td>
                            <td>Обрабатывать статьи, опубликованные не ранее указанного количества дней назад. Старые архивные статьи не трогаются. <code>0</code> &mdash; без ограничения по возрасту.</td>
                        </tr>
                        <tr>
                            <td><strong>Размер пачки</strong><br><small class="text-muted"><code>batch_limit</code></small></td>
                            <td><?php echo (int)($options['batch_limit'] ?? 15); ?> записей</td>
                            <td>Максимальное количество случайно выбираемых записей (<code>LIMIT N</code>) за один запуск. Гарантирует миллисекундное выполнение SQL-запроса без блокировок таблиц.</td>
                        </tr>
                        <tr>
                            <td><strong>Суточная кривая</strong><br><small class="text-muted"><code>use_diurnal_curve</code></small></td>
                            <td>
                                <?php if (!empty($options['use_diurnal_curve'])) { ?>
                                    <span class="text-success font-weight-bold">Включена</span>
                                <?php } else { ?>
                                    <span class="text-muted">Отключена</span>
                                <?php } ?>
                            </td>
                            <td>Масштабирует шаг накрутки в зависимости от текущего часа суток. Значения множителей для каждого времени суток настраиваются индивидуально.</td>
                        </tr>
                        <tr>
                            <td><strong>Пауза ночью</strong><br><small class="text-muted"><code>disable_at_night</code></small></td>
                            <td>
                                <?php if (!empty($options['disable_at_night'])) { ?>
                                    <span class="text-danger font-weight-bold">Активна (02:00-06:00)</span>
                                <?php } else { ?>
                                    <span class="text-muted">Выключена</span>
                                <?php } ?>
                            </td>
                            <td>Полностью блокирует запуск накрутки в интервале с 02:00 до 06:00 ночи для максимальной естественности графика посещаемости.</td>
                        </tr>
                        <tr>
                            <td><strong>Множители суточной кривой</strong><br><small class="text-muted"><code>curve_*</code></small></td>
                            <td>
                                Ночь: <?php echo number_format($curve_night, 2); ?>,
                                Утро: <?php echo number_format($curve_morning, 2); ?>,
                                День: <?php echo number_format($curve_day, 2); ?>,
                                Вечер: <?php echo number_format($curve_evening, 2); ?>,
                                Поздний: <?php echo number_format($curve_late, 2); ?>
                            </td>
                            <td>Индивидуальные весовые коэффициенты шага для пяти временных интервалов суток. Настраиваются во вкладке «Опции».</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Diurnal Curve Card -->
        <div class="card mb-4">
            <div class="card-header font-weight-bold d-flex align-items-center justify-content-between">
                <span><?php echo html(defined('LANG_HITSBOOSTER_HELP_DIURNAL_TITLE') ? LANG_HITSBOOSTER_HELP_DIURNAL_TITLE : 'Суточная кривая активности'); ?></span>
                <a href="<?php echo href_to($this->controller->root_url, 'options'); ?>" class="btn btn-sm btn-primary">
                    <?php echo defined('LANG_OPTIONS') ? LANG_OPTIONS : 'Опции'; ?>
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0 text-center">
                    <thead>
                        <tr>
                            <th>Интервал суток</th>
                            <th>Множитель</th>
                            <th>Период</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>02:00 &mdash; 05:59</code></td>
                            <td><?php echo number_format($curve_night, 2); ?> &times;</td>
                            <td><span class="text-muted">Глубокая ночь</span></td>
                        </tr>
                        <tr>
                            <td><code>06:00 &mdash; 08:59</code></td>
                            <td><?php echo number_format($curve_morning, 2); ?> &times;</td>
                            <td><span class="text-muted">Утренний подъем</span></td>
                        </tr>
                        <tr>
                            <td><code>09:00 &mdash; 17:59</code></td>
                            <td><?php echo number_format($curve_day, 2); ?> &times;</td>
                            <td><span class="text-muted">Рабочее время</span></td>
                        </tr>
                        <tr>
                            <td><code>18:00 &mdash; 22:59</code></td>
                            <td><?php echo number_format($curve_evening, 2); ?> &times;</td>
                            <td><span class="text-muted">Вечерний пик</span></td>
                        </tr>
                        <tr>
                            <td><code>23:00 &mdash; 01:59</code></td>
                            <td><?php echo number_format($curve_late, 2); ?> &times;</td>
                            <td><span class="text-muted">Поздний вечер</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted small text-center">
                Значения множителей можно изменить во вкладке «Опции».
            </div>
        </div>

        <!-- Scheduler Card -->
        <div class="card mb-4">
            <div class="card-header font-weight-bold">
                <?php echo html(defined('LANG_HITSBOOSTER_HELP_CRON_TITLE') ? LANG_HITSBOOSTER_HELP_CRON_TITLE : 'Настройка планировщика (CRON)'); ?>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    <?php echo html(defined('LANG_HITSBOOSTER_HELP_CRON_HINT') ? LANG_HITSBOOSTER_HELP_CRON_HINT : 'Все операции выполняются исключительно в фоновом режиме через планировщик задач InstantCMS.'); ?>
                </p>

                <?php if ($scheduler_task) { ?>
                    <ul class="list-unstyled small mb-3">
                        <li class="mb-1"><strong>Периодичность:</strong> раз в <?php echo (int)$scheduler_task['period']; ?> мин.</li>
                        <li class="mb-1"><strong>Статус:</strong> <?php echo !empty($scheduler_task['is_active']) ? '<span class="text-success font-weight-bold">Активна</span>' : '<span class="text-danger">Отключена</span>'; ?></li>
                        <li class="mb-1"><strong>Последний запуск:</strong> <?php echo $scheduler_task['date_last_run'] ? html_date_time($scheduler_task['date_last_run']) : 'Еще не запускалась'; ?></li>
                    </ul>
                    <a href="<?php echo href_to('admin', 'settings', 'scheduler'); ?>" class="btn btn-sm btn-secondary btn-block">
                        <?php echo defined('LANG_HITSBOOSTER_CRON_MANAGE') ? LANG_HITSBOOSTER_CRON_MANAGE : 'Управление планировщиком'; ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
