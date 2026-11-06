<?php

class onHitsboosterCronBoost extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($data = null) {
        $this->model->runBoost($this->controller->options);
        return $data;
    }

}
