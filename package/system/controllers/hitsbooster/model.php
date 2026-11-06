<?php

class modelHitsbooster extends cmsModel {

    /**
     * Возвращает коэффициент интенсивности просмотров в зависимости от текущего часа суток.
     */
    public function getHourlyMultiplier(array $options = []): float {
        $hour = (int)date('G');

        $curveNight   = isset($options['curve_night']) && is_numeric($options['curve_night']) ? (float)$options['curve_night'] : 0.15;
        $curveMorning = isset($options['curve_morning']) && is_numeric($options['curve_morning']) ? (float)$options['curve_morning'] : 0.60;
        $curveDay     = isset($options['curve_day']) && is_numeric($options['curve_day']) ? (float)$options['curve_day'] : 1.00;
        $curveEvening = isset($options['curve_evening']) && is_numeric($options['curve_evening']) ? (float)$options['curve_evening'] : 1.35;
        $curveLate    = isset($options['curve_late']) && is_numeric($options['curve_late']) ? (float)$options['curve_late'] : 0.40;

        // Распределение по времени суток
        if ($hour >= 2 && $hour < 6) {
            return $curveNight;
        }
        if ($hour >= 6 && $hour < 9) {
            return $curveMorning;
        }
        if ($hour >= 9 && $hour < 18) {
            return $curveDay;
        }
        if ($hour >= 18 && $hour < 23) {
            return $curveEvening;
        }

        return $curveLate;
    }

    /**
     * Запуск накрутки по всем настроенным типам контента.
     */
    public function runBoost(array $options = []): bool {
        if (!$options) {
            $options = cmsController::loadOptions('hitsbooster');
        }

        if (empty($options['ctypes']) || !is_array($options['ctypes'])) {
            return true;
        }

        $ctypes = $options['ctypes'];
        if (in_array(0, $ctypes, false) || in_array('0', $ctypes, true)) {
            $all = cmsCore::getModel('content')->getContentTypes();
            $ctypes = array_column($all, 'name');
        }

        foreach ($ctypes as $ctypeName) {
            if (!$ctypeName) {
                continue;
            }
            $this->boostContentTypeHits((string)$ctypeName, $options);
        }

        return true;
    }

    /**
     * Определение имени поля просмотров (hits_count в стандартном InstantCMS или hits в кастомных таблицах).
     */
    public function getHitsFieldName(string $table): ?string {
        if ($this->db->isFieldExists($table, 'hits_count')) {
            return 'hits_count';
        }
        if ($this->db->isFieldExists($table, 'hits')) {
            return 'hits';
        }
        return null;
    }

    /**
     * Пакетное обновление просмотров по типу контента.
     */
    public function boostContentTypeHits(string $ctypeName, array $options): void {
        $ctypeName = preg_replace('/[^a-z0-9_]/i', '', $ctypeName);
        if (!$ctypeName) {
            return;
        }

        $table = 'con_' . $ctypeName;

        if (!$this->db->isTableExists($table)) {
            return;
        }

        $hitsField = $this->getHitsFieldName($table);
        if (!$hitsField) {
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
            $multiplier = $this->getHourlyMultiplier($options);
            $minStep = max(1, (int)round($minStep * $multiplier));
            $maxStep = max($minStep, (int)round($maxStep * $multiplier));
        }

        // Формирование условий фильтрации
        $where = [
            'is_pub = 1',
            "{$hitsField} < {$maxHits}"
        ];

        if ($this->db->isFieldExists($table, 'is_deleted')) {
            $where[] = 'is_deleted IS NULL';
        }

        if ($this->db->isFieldExists($table, 'is_approved')) {
            $where[] = 'is_approved = 1';
        }

        if ($minOrganic > 0) {
            $where[] = "{$hitsField} >= {$minOrganic}";
        }

        if ($daysLimit > 0) {
            $where[] = "date_pub >= DATE_SUB(NOW(), INTERVAL {$daysLimit} DAY)";
        }

        $whereSql = implode(' AND ', $where);

        // Атомарное увеличение поля просмотров для случайной пачки записей
        $sql = "UPDATE {#}{$table}
                SET {$hitsField} = {$hitsField} + FLOOR({$minStep} + (RAND() * ({$maxStep} - {$minStep} + 1)))
                WHERE {$whereSql}
                ORDER BY RAND()
                LIMIT {$batchLimit}";

        $this->db->query($sql);
    }

}
