<?php

class actionHitsboosterHelp extends cmsAction {

    public function run() {
        $ctypes = cmsCore::getModel('content')->getContentTypes();
        $scheduler_task = cmsDatabase::getInstance()->getRow('scheduler_tasks', "controller = 'hitsbooster'");

        return $this->cms_template->render('backend/help', [
            'options'        => $this->controller->options,
            'controller'     => $this->controller,
            'ctypes'         => $ctypes,
            'scheduler_task' => $scheduler_task
        ]);
    }

}
