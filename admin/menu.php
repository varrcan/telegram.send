<?php
$menu = [
    [
        'parent_menu' => 'global_menu_services',
        'sort'        => 1000,
        'text'        => 'Телеграм',
        'title'       => 'Телеграм',
        'items_id'    => 'menu_references',
        'icon'        => '',
        'items'       => [
            [
                'text'  => 'Настройки',
                'url'   => 'telegram_main.php?lang=' . LANGUAGE_ID,
                'title' => 'Настройки',
            ],
            [
                'text'  => 'Инструкция',
                'url'   => 'telegram_instruction.php?lang=' . LANGUAGE_ID,
                'title' => 'Регистрация бота',
            ],
        ],
    ],
];

return $menu;
