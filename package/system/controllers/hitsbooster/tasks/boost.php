<?php

if (!class_exists('cmsCronTask')) {
    class cmsCronTask {
        public function run(): bool {
            return true;
        }
    }
}

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

        return $model->runBoost($options);
    }

}
