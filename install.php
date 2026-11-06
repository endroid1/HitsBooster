<?php

function install_package() {
    $db = cmsDatabase::getInstance();

    $taskExists = $db->getRowsCount('scheduler_tasks', "controller = 'hitsbooster' AND hook = 'boost'");

    if (!$taskExists) {
        $db->insert('scheduler_tasks', [
            'title'       => 'HitsBooster: Накрутка просмотров контента',
            'controller'  => 'hitsbooster',
            'hook'        => 'boost',
            'period'      => 15,
            'is_strict'   => 0,
            'is_active'   => 1
        ]);
    }

    return true;
}

function after_install_package() {
    return true;
}
