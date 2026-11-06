<?php

class hitsbooster extends cmsFrontend {

    protected $useOptions = true;

    public function actionIndex() {
        return cmsCore::error404();
    }

}
