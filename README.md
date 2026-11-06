# HitsBooster for InstantCMS 2

[![InstantCMS](https://img.shields.io/badge/InstantCMS-2.18%2B-blue.svg)](https://instantcms.ru)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4.svg)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.0.0-orange.svg)](https://github.com/endroid1/HitsBooster)

[Русский](#русский) | [English](#english) | [中文](#中文)

---

<a name="русский"></a>
## Русский

**HitsBooster** — легковесный компонент для InstantCMS 2.18+, предназначенный для автоматического, плавного и естественного увеличения счетчиков просмотров (`hits_count`) опубликованных материалов.

Помогает решить проблему «холодного старта» для новых сайтов и развивающихся блогов: формирует социальное доказательство активности и повышает доверие посетителей к контенту без шаблонности и неестественных всплесков.

### Ключевые возможности

* **Нулевой оверхед для посетителей:** накрутка не запускается при посещении страниц пользователями сайта. Вся работа выполняется строго в фоновом режиме через планировщик задач (CRON).
* **Атомарные прямые SQL-запросы:** прямое пакетное обновление счетчика в базе данных без тяжелой загрузки ORM-моделей и без изменения даты последнего редактирования записей.
* **Суточная кривая активности:** естественное масштабирование шага просмотров в зависимости от времени суток:
  * Ночь (02:00 – 05:59): коэффициент 0.15 (или полная пауза)
  * Утро (06:00 – 08:59): коэффициент 0.60
  * День (09:00 – 17:59): коэффициент 1.00
  * Вечер (18:00 – 22:59): коэффициент 1.35
  * Поздний вечер (23:00 – 01:59): коэффициент 0.40
* **Ночная пауза:** опция автоматической приостановки накрутки с 02:00 до 06:00 для максимальной естественности.
* **Рандомизация:** случайная выборка материалов из базы и случайный шаг прироста просмотров (например, от 1 до 5 просмотров за один запуск).
* **Умные лимиты и предохранители:**
  * **Потолок просмотров (`max_hits`):** статьи, набравшие заданный лимит (например, 1000 просмотров), автоматически исключаются из накрутки.
  * **Порог старта (`min_organic_hits`):** возможность начинать накрутку только после того, как материал наберет первые реальные органические просмотры от живых посетителей.
  * **Возраст записей (`days_limit`):** обработка только свежих материалов (например, за последние 7 дней) без затрагивания старых архивов.
  * **Выбор типов контента (`ctypes`):** гибкий выбор разделов сайта (статьи, новости, блоги, объявления и т.д.).
* **Нативная админ-панель:** полное соответствие стилям и цветам InstantCMS (`admincoreui` / Bootstrap 4), отдельная подробная вкладка справки со всеми пояснениями и статусом CRON.

### Системные требования

* InstantCMS 2.18.0 или выше (протестировано на 2.18.2)
* PHP 8.3 или выше (совместимо с PHP 8.4)
* MySQL 5.7+ / MariaDB 10.3+

### Установка

1. Скачайте готовый архив `hitsbooster.zip` из репозитория.
2. Перейдите в панель управления InstantCMS: **Управление &rarr; Установка дополнений** (`/admin/install`).
3. Загрузите архив `hitsbooster.zip` и следуйте инструкциям установщика.
4. После завершения установки перейдите в **Компоненты &rarr; HitsBooster** (`/admin/controllers/edit/hitsbooster`) и настройте параметры под ваш сайт.
5. Убедитесь, что планировщик задач InstantCMS настроен на системный CRON:
   ```bash
   * * * * * php /путь/к/сайту/cron.php sitename.ru
   ```

---

<a name="english"></a>
## English

**HitsBooster** is a lightweight component for InstantCMS 2.18+ designed for automatic, smooth, and natural incrementation of article view counters (`hits_count`).

It solves the cold-start problem for new websites and growing blogs: building social proof and increasing visitor engagement without artificial spikes or server strain.

### Key Features

* **Zero visitor overhead:** never runs on visitor page loads. All processing is decoupled and handled in the background by the InstantCMS scheduler (CRON).
* **Atomic database updates:** direct, batch SQL operations update hits without ORM model overhead and without altering `date_last_modified`.
* **Diurnal activity curve:** scales increment step based on the hour of the day:
  * Deep night (02:00 – 05:59): 0.15 multiplier (or complete sleep)
  * Morning (06:00 – 08:59): 0.60 multiplier
  * Daytime (09:00 – 17:59): 1.00 multiplier
  * Evening peak (18:00 – 22:59): 1.35 multiplier
  * Late evening (23:00 – 01:59): 0.40 multiplier
* **Night pause:** optional complete quiet period between 02:00 and 06:00.
* **Full randomization:** randomized article selection and random step ranges (e.g. 1 to 5 hits per run).
* **Smart boundaries:**
  * **Ceiling limit (`max_hits`):** automatically stops boosting items that reached the target count.
  * **Organic threshold (`min_organic_hits`):** start boosting only after an article gets initial organic views.
  * **Age limit (`days_limit`):** process only recent articles (e.g. last 7 days), leaving archives untouched.
  * **Content types selection (`ctypes`):** select any combination of content types.
* **Native admin UI:** strictly follows InstantCMS `admincoreui` / Bootstrap 4 styling with a comprehensive built-in documentation and status tab.

### Requirements

* InstantCMS 2.18.0+ (tested on 2.18.2)
* PHP 8.3+ (fully compatible with PHP 8.4)
* MySQL 5.7+ / MariaDB 10.3+

### Installation

1. Download `hitsbooster.zip` from this repository.
2. In your InstantCMS admin panel, navigate to **Management &rarr; Install Addons** (`/admin/install`).
3. Upload `hitsbooster.zip` and complete the installation wizard.
4. Open **Components &rarr; HitsBooster** (`/admin/controllers/edit/hitsbooster`) and configure options.
5. Ensure your InstantCMS scheduler is wired to system CRON:
   ```bash
   * * * * * php /path/to/site/cron.php sitename.com
   ```

---

<a name="中文"></a>
## 中文

**HitsBooster** 是专为 InstantCMS 2.18+ 设计的轻量级组件，用于在后台自动、平滑且符合自然规律地增加已发布内容的浏览量计数（`hits_count`）。

有效解决新站及发展中博客的“冷启动”难题：建立良好的受众信任度与内容热度，避免机械化暴涨与服务器性能损耗。

### 核心功能

* **对访客零负担：** 绝不在真实访客浏览网页时触发任何增量脚本，全部工作由系统后台计划任务（CRON）异步完成。
* **极速原子级 SQL 更新：** 直接批量更新数据库浏览字段，不加载沉重的 ORM 模型，不篡改文章最后修改时间。
* **拟真昼夜活动曲线：** 根据一天中不同时间段动态调整增长倍率：
  * 深夜（02:00 – 05:59）：0.15 倍率（或自动暂停）
  * 早晨（06:00 – 08:59）：0.60 倍率
  * 白天（09:00 – 17:59）：1.00 倍率
  * 傍晚高峰（18:00 – 22:59）：1.35 倍率
  * 深夜前夕（23:00 – 01:59）：0.40 倍率
* **夜间静默保护：** 支持在凌晨 02:00 至 06:00 完全暂停增长，数据走势真实自然。
* **完全随机化：** 随机抽取候选文章，增量步长（如单次 1~5 浏览量）随机浮动，告别机械死板。
* **安全阈值与过滤器：**
  * **浏览量封顶（`max_hits`）：** 达到指定上限（如 1000 次）自动退出增长。
  * **冷启动门槛（`min_organic_hits`）：** 仅在获得一定真实自然阅读量后再行增长。
  * **发布时效限制（`days_limit`）：** 仅对近期文章生效（如 7 天内），保护旧归档。
  * **多栏目勾选（`ctypes`）：** 自由选定参与的内容模型。
* **原生后台设计：** 严格遵照 InstantCMS `admincoreui` / Bootstrap 4 界面风格，内置详细说明与 CRON 状态看板。

### 环境要求

* InstantCMS 2.18.0 或更高版本（已在 2.18.2 测试）
* PHP 8.3 或更高版本（兼容 PHP 8.4）
* MySQL 5.7+ / MariaDB 10.3+

### 安装步骤

1. 从本仓库下载安装包 `hitsbooster.zip`。
2. 登录 InstantCMS 管理后台，进入 **管理 &rarr; 安装扩展包**（`/admin/install`）。
3. 上传 `hitsbooster.zip` 并按提示完成安装。
4. 进入 **组件 &rarr; HitsBooster**（`/admin/controllers/edit/hitsbooster`）进行参数配置。
5. 确认已配置系统 CRON 调度：
   ```bash
   * * * * * php /path/to/site/cron.php sitename.com
   ```

---

## License / Лицензия / 许可证

[MIT License](LICENSE) &copy; 2026 [endroid](https://3va.ru)
