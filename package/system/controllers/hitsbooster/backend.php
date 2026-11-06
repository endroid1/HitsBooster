<?php

class backendHitsbooster extends cmsBackend {

    public $useDefaultOptionsAction = true;
    protected $useOptions = true;

    public function getBackendMenu() {
        return [
            [
                'title'   => defined('LANG_OPTIONS') ? LANG_OPTIONS : 'Опции',
                'url'     => href_to($this->root_url, 'options'),
                'options' => ['icon' => 'cog']
            ],
            [
                'title'   => defined('LANG_HITSBOOSTER_CP_HELP') ? LANG_HITSBOOSTER_CP_HELP : 'Инструкция и описание',
                'url'     => href_to($this->root_url, 'help'),
                'options' => ['icon' => 'info-circle']
            ]
        ];
    }

    public function getBackendSubMenu() {
        return $this->backend_sub_menu;
    }

    public function getOptionsToolbar() {
        return [];
    }

}
