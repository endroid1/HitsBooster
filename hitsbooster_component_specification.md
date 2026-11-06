# Спецификация компонента HitsBooster для InstantCMS v2/v3

Компонент **HitsBooster** предназначен для автоматического плавного увеличения количества просмотров (`hits`) опубликованных материалов с использованием встроенного планировщика задач (CRON/Tasks) InstantCMS.

---

## 1. Архитектура и принцип работы

1. **Фоновая обработка:** Накрутка не выполняется во время генерации клиентских страниц (что исключает задержки ответа сервера для реальных посетителей). Вся работа делегирована системному планировщику (`cmsCronTask`).
2. **Атомарные SQL-операции:** Увеличение просмотров выполняется пакетным запросом напрямую к полю `hits` целевой таблицы типа контента `{#}con_{ctype_name}`. Это исключает тяжелую загрузку ORM-моделей и не триггерит системные события изменения даты материала (`date_last_modified`).
3. **Рандомизация и безопасность:**
   - Каждое обновление выбирает случайные статьи (`ORDER BY RAND() LIMIT N`), исключая шаблонность.
   - Значение прибавки вычисляется случайно в заданном диапазоне `[min_step, max_step]`.
   - Встроен суточный коэффициент активности, масштабирующий или полностью отключающий накрутку в ночные часы.

---

## 2. Структура файлов компонента

```text
package/
└── system/
    └── controllers/
        └── hitsbooster/
            ├── backend.php                      # Панель управления и сохранение настроек
            ├── backend/
            │   └── forms/
            │       └── form_options.php         # Форма настроек в админке
            ├── frontend.php                     # Базовый класс контроллера
            ├── model.php                        # Модель работы с БД и расчет коэффициентов
            └── tasks/
                └── boost.php                    # Задача для планировщика InstantCMS
```

---

## 3. Исходный код модулей

### 3.1. Форма настроек (`backend/forms/form_options.php`)

```php
<?php

class formHitsboosterOptions extends cmsForm {

    public function init() {
        return [
            [
                'type' => 'fieldset',
                'title' => 'Параметры накрутки просмотров',
                'childs' => [
                    new fieldListMultiple('ctypes', [
                        'title' => 'Типы контента для обработки',
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
                        'title' => 'Минимальная прибавка за раз',
                        'default' => 1,
                        'rules' => [['min', 1]]
                    ]),
                    new fieldNumber('max_step', [
                        'title' => 'Максимальная прибавка за раз',
                        'default' => 5,
                        'rules' => [['min', 1]]
                    ]),
                    new fieldNumber('max_hits', [
                        'title' => 'Останавливать при достижении просмотров',
                        'hint' => 'Материалы, у которых hits >= этому значению, исключаются из накрутки',
                        'default' => 1000
                    ]),
                    new fieldNumber('min_organic_hits', [
                        'title' => 'Порог старта (минимальные органические просмотры)',
                        'hint' => 'Материал начнет накручиваться только после достижения указанного числа реальных просмотров (0 — накручивать сразу)',
                        'default' => 0
                    ]),
                    new fieldNumber('days_limit', [
                        'title' => 'Возраст записей (дней)',
                        'hint' => 'Обрабатывать материалы, опубликованные не ранее указанного срока (0 — без ограничений)',
                        'default' => 7
                    ]),
                    new fieldNumber('batch_limit', [
                        'title' => 'Размер выборки за один запуск (LIMIT)',
                        'hint' => 'Число случайно выбираемых записей для снижения нагрузки на БД',
                        'default' => 15
                    ]),
                    new fieldCheckbox('use_diurnal_curve', [
                        'title' => 'Использовать суточный коэффициент активности',
                        'hint' => 'Снижает прирост ночью (01:00 - 06:59) и увеличивает в дневной и вечерний прайм-тайм',
                        'default' => 1
                    ]),
                    new fieldCheckbox('disable_at_night', [
                        'title' => 'Полная пауза в ночное время',
                        'hint' => 'Отключать выполнение накрутки с 02:00 до 06:00',
                        'default' => 0
                    ])
                ]
            ]
        ];
    }
}
```

---

### 3.2. Модель и суточная кривая (`model.php`)

```php
<?php

class modelHitsbooster extends cmsModel {

    /**
     * Возвращает коэффициент интенсивности просмотров в зависимости от текущего часа суток.
     */
    public function getHourlyMultiplier(): float {
        $hour = (int)date('G');

        // Распределение по времени суток
        if ($hour >= 2 && $hour < 6) {
            return 0.15; // Глубокая ночь (минимальный прирост)
        }
        if ($hour >= 6 && $hour < 9) {
            return 0.60; // Утренний подъем
        }
        if ($hour >= 9 && $hour < 18) {
            return 1.00; // Стандартное рабочее время
        }
        if ($hour >= 18 && $hour < 23) {
            return 1.35; // Вечерний пик посещаемости
        }
        
        return 0.40; // Поздний вечер (23:00 - 02:00)
    }

    /**
     * Пакетное обновление просмотров по типу контента.
     */
    public function boostContentTypeHits(string $ctypeName, array $options): void {
        $table = 'con_' . $ctypeName;

        if (!$this->db->isTableExists($table)) {
            return;
        }

        $hour = (int)date('G');
        if (!empty($options['disable_at_night']) && ($hour >= 2 && $hour < 6)) {
            return;
        }

        $minStep    = max(1, (int)($options['min_step'] ?? 1));
        $maxStep    = max($minStep, (int)($options['max_step'] ?? 5));
        $maxHits    = (int)($options['max_hits'] ?? 1000);
        $minOrganic = max(0, (int)($options['min_organic_hits'] ?? 0));
        $batchLimit = max(1, (int)($options['batch_limit'] ?? 15));
        $daysLimit  = (int)($options['days_limit'] ?? 0);

        // Применение суточного множителя
        if (!empty($options['use_diurnal_curve'])) {
            $multiplier = $this->getHourlyMultiplier();
            $minStep = max(1, (int)round($minStep * $multiplier));
            $maxStep = max($minStep, (int)round($maxStep * $multiplier));
        }

        // Формирование условий фильтрации
        $where = [
            'is_pub = 1',
            "hits < {$maxHits}"
        ];

        if ($minOrganic > 0) {
            $where[] = "hits >= {$minOrganic}";
        }

        if ($daysLimit > 0) {
            $where[] = "date_pub >= DATE_SUB(NOW(), INTERVAL {$daysLimit} DAY)";
        }

        $whereSql = implode(' AND ', $where);

        // Атомарное увеличение поля hits для случайной пачки записей
        $sql = "UPDATE {#}{$table}
                SET hits = hits + FLOOR({$minStep} + (RAND() * ({$maxStep} - {$minStep} + 1)))
                WHERE {$whereSql}
                ORDER BY RAND()
                LIMIT {$batchLimit}";

        $this->db->query($sql);
    }
}
```

---

### 3.3. Задача планировщика (`tasks/boost.php`)

```php
<?php

class onHitsboosterBoost extends cmsCronTask {

    public function run(): bool {
        /** @var hitsbooster $controller */
        $controller = cmsCore::getController('hitsbooster');
        $options    = $controller->options;

        if (empty($options['ctypes']) || !is_array($options['ctypes'])) {
            return true;
        }

        /** @var modelHitsbooster $model */
        $model = $controller->model;

        foreach ($options['ctypes'] as $ctypeName) {
            $model->boostContentTypeHits((string)$ctypeName, $options);
        }

        return true;
    }
}
```

---

### 3.4. Базовый контроллер (`frontend.php`)

```php
<?php

class hitsbooster extends cmsFrontend {}
```

---

### 3.5. Бэкенд админки (`backend.php`)

```php
<?php

class backendHitsbooster extends cmsBackend {

    public function actionIndex() {
        $this->redirectToAction('options');
    }

    public function actionOptions() {
        $form = $this->getForm('options');
        $isSubmitted = $this->request->has('submit');

        if ($isSubmitted) {
            $options = $form->parse($this->request, $isSubmitted);
            $errors  = $form->validate($this, $options);

            if (!$errors) {
                cmsCore::getModel('controllers')->saveControllerOptions($this->name, $options);
                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
                $this->redirectToAction('options');
            }

            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
        } else {
            $options = $this->options;
        }

        return cmsTemplate::getInstance()->render('backend/options', [
            'form'    => $form,
            'options' => $options,
            'errors'  => $errors ?? false
        ]);
    }
}
```

---

## 4. Регистрация задачи в планировщике (Установка)

В установочном файле `install.php` регистрируется фоновый таск:

```php
<?php

function install_package() {
    $db = cmsDatabase::getInstance();

    $taskExists = $db->getRowsCount('scheduler_tasks', "controller = 'hitsbooster' AND hook = 'boost'");

    if (!$taskExists) {
        $db->insert('scheduler_tasks', [
            'title'       => 'HitsBooster: Накрутка просмотров контента',
            'controller'  => 'hitsbooster',
            'hook'        => 'boost',
            'period'      => 15, // Запуск каждые 15 минут
            'is_strict'   => 0,
            'is_active'   => 1
        ]);
    }

    return true;
}
```

---

## 5. Сводка поддерживаемых улучшений

| Функция | Описание | Эффект |
| :--- | :--- | :--- |
| **Случайный шаг (`min_step` – `max_step`)** | Рандомный инкремент при каждом обновлении | Устраняет шаблонность и подозрительные одинаковые прибавки |
| **Суточная кривая (Diurnal Curve)** | Зависимость шага от текущего часа суток | Реалистичная динамика: спад ночью, пик вечером |
| **Порог органики (`min_organic_hits`)** | Старт только после N реальных просмотров | Новые и пустые статьи не начинают набирать просмотры неестественно |
| **Пакетная обработка (`batch_limit`)** | `LIMIT N` в сочетании с `ORDER BY RAND()` | Защита от блокировок таблиц и нулевое влияние на производительность |
| **Потолок просмотров (`max_hits`)** | Остановка при достижении заданного числа | Предотвращает раздувание статистики до аномальных величин |
| **Окно актуальности (`days_limit`)** | Накрутка только для материалов моложе N дней | Старые архивные статьи перестают обновляться |